<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Validator;

use App\Helpers\ValidationDefaults;
use App\Models\Product;

class ProductController extends Controller {

    /**
     * Products index page.
     * This shows the list of products in the current economy.
     *
     * @return Response
     */
    public function index($communityId, $economyId) {
        // Get the user, community, find the products
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $products = $economy->products;

        return view('community.economy.product.index')
            ->with('economy', $economy)
            ->with('products', $products);
    }

    /**
     * Product creation page.
     *
     * @return Response
     */
    public function create($communityId, $economyId) {
        // Get the user, community, find the products
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $locales = langManager()->getLocales(true, true);

        return view('community.economy.product.create')
            ->with('economy', $economy)
            ->with('locales', $locales);
    }

    /**
     * Product create endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doCreate(Request $request, $communityId, $economyId) {
        // Get the user, community, find the products
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $locales = collect(langManager()->getLocales(true, true));

        // Build validation rules, and validate
        $rules = ['name' => 'required|' . ValidationDefaults::NAME];
        $messages = [];
        foreach($economy->currencies as $currency) {
            $rules['price_' . $currency->id] = 'nullable|' . ValidationDefaults::PRICE;
            $messages['price_' . $currency->id . '.regex'] = __('misc.invalidPrice');
        }
        foreach($locales as $locale)
            $rules['name_' . $locale] = 'nullable|' . ValidationDefaults::NAME;
        $this->validate($request, $rules, $messages);

        // Create product and set prices in transaction
        DB::transaction(function() use($request, $economy, $locales) {
            // Create the product
            $product = $economy->products()->create([
                'economy_id' => $economy->id,
                'type' => Product::TYPE_NORMAL,
                'name' => $request->input('name'),
                'enabled' => is_checked($request->input('enabled')),
                'archived' => is_checked($request->input('archived')),
            ]);

            // Create the localized product names
            $product->names()->createMany(
                $locales
                    ->filter(function($locale) use($request) {
                        return $request->input('name_' . $locale) != null;
                    })
                    ->map(function($locale) use($request, $product) {
                        return [
                            'product_id' => $product->id,
                            'locale' => $locale,
                            'name' => $request->input('name_' . $locale),
                        ];
                    })
                    ->toArray()
            );

            // Create the product prices
            $product->prices()->createMany(
                $economy
                    ->currencies
                    ->filter(function($currency) use($request) {
                        return $request->input('price_' . $currency->id) != null;
                    })
                    ->map(function($currency) use($request, $product) {
                        return [
                            'product_id' => $product->id,
                            'currency_id' => $currency->id,
                            'price' => str_replace(',', '.', $request->input('price_' . $currency->id)),
                        ];
                    })
                    ->toArray()
            );
        });

        // Redirect the user to the product index
        return redirect()
            ->route('community.economy.product.index', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
            ])
            ->with('success', __('pages.products.created'));
    }

    /**
     * Show a product.
     *
     * @return Response
     */
    public function show($communityId, $economyId, $productId) {
        // Get the user, community, find the product
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $product = $economy->products()->findOrFail($productId);

        return view('community.economy.product.show')
            ->with('economy', $economy)
            ->with('product', $product);
    }

    /**
     * Edit a product.
     *
     * @return Response
     */
    public function edit($communityId, $economyId, $productId) {
        // Get the user, community, find the product
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $product = $economy->products()->findOrFail($productId);
        $locales = langManager()->getLocales(true, true);

        return view('community.economy.product.edit')
            ->with('economy', $economy)
            ->with('product', $product)
            ->with('locales', $locales);
    }

    /**
     * Product update endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doEdit(Request $request, $communityId, $economyId, $productId) {
        // Get the user, community, find the product
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $product = $economy->products()->findOrFail($productId);
        $locales = collect(langManager()->getLocales(true, true));

        // Build validation rules, and validate
        $rules = ['name' => 'required|' . ValidationDefaults::NAME];
        $messages = [];
        foreach($economy->currencies as $currency) {
            $rules['price_' . $currency->id] = 'nullable|' . ValidationDefaults::PRICE;
            $messages['price_' . $currency->id . '.regex'] = __('misc.invalidPrice');
        }
        foreach($locales as $locale)
            $rules['name_' . $locale] = 'nullable|' . ValidationDefaults::NAME;
        $this->validate($request, $rules, $messages);

        // Change product properties and sync prices in transaction
        DB::transaction(function() use($request, $product, $economy, $locales) {
            // Change properties
            $product->name = $request->input('name');
            $product->enabled = is_checked($request->input('enabled'));
            $product->archived = is_checked($request->input('archived'));
            $product->save();

            // Sync localized product names
            $product->names()->sync(
                $locales
                    ->filter(function($locale) use($request) {
                        return $request->input('name_' . $locale) != null;
                    })
                    ->map(function($locale) use($request, $product) {
                        return [
                            'id' => $product
                                ->names
                                ->filter(function($n) use($locale) { return $n->locale == $locale; })
                                ->map(function($n) { return $n->id; })
                                ->first(),
                            'product_id' => $product->id,
                            'locale' => $locale,
                            'name' => $request->input('name_' . $locale),
                        ];
                    })
                    ->toArray()
            );

            // Sync product prices
            $product->prices()->sync(
                $economy
                    ->currencies
                    ->filter(function($currency) use($request) {
                        return $request->input('price_' . $currency->id) != null;
                    })
                    ->map(function($currency) use($request, $product) {
                        return [
                            'id' => $product
                                ->prices
                                ->filter(function($p) use($currency) { return $p->currency_id == $currency->id; })
                                ->map(function($p) { return $p->id; })
                                ->first(),
                            'product_id' => $product->id,
                            'currency_id' => $currency->id,
                            'price' => str_replace(',', '.', $request->input('price_' . $currency->id)),
                        ];
                    })
                    ->toArray()
            );
        });

        // Redirect the user to the account overview page
        return redirect()
            ->route('community.economy.product.show', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'productId' => $product->id,
            ])
            ->with('success', __('pages.products.changed'));
    }

    /**
     * Page for confirming the deletion of the product.
     *
     * @return Response
     */
    public function delete($communityId, $economyId, $productId) {
        // TODO: suggest to archive instead!

        // Get the user, community, find the product
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $product = $economy->products()->findOrFail($productId);

        // TODO: ensure there are no other constraints that prevent deleting the
        // product

        return view('community.economy.product.delete')
            ->with('economy', $economy)
            ->with('product', $product);
    }

    /**
     * Delete a product.
     *
     * @return Response
     */
    public function doDelete($communityId, $economyId, $productId) {
        // Get the user, community, find the product
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $product = $economy->products()->findOrFail($productId);

        // TODO: ensure there are no other constraints that prevent deleting the
        // product

        // Delete the product
        $product->delete();

        // Redirect to the product index
        return redirect()
            ->route('community.economy.product.index', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id
            ])
            ->with('success', __('pages.products.deleted'));
    }

    /**
     * The permission required for viewing.
     * @return PermsConfig The permission configuration.
     */
    public static function permsView() {
        return EconomyController::permsView();
    }

    /**
     * The permission required for managing such as editing and deleting.
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        return EconomyController::permsManage();
    }
}