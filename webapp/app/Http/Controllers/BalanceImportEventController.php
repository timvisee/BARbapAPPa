<?php

namespace App\Http\Controllers;

use App\Mail\BalanceImport\Update;
use App\Models\MutationWallet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

use App\Helpers\ValidationDefaults;

class BalanceImportEventController extends Controller {

    /**
     * Balance import event index page.
     * This shows a list of registered balance import events for a balance import
     * system.
     *
     * @return Response
     */
    public function index(Request $request, $communityId, $economyId, $systemId) {
        // Get the community, economy, system and events
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $events = $system->events;

        return view('community.economy.balanceimport.event.index')
            ->with('economy', $economy)
            ->with('system', $system)
            ->with('events', $events);
    }

    /**
     * Balance import event creation page.
     *
     * @return Response
     */
    public function create(Request $request, $communityId, $economyId, $systemId) {
        // Get the community, economy and find the system
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);

        return view('community.economy.balanceimport.event.create')
            ->with('economy', $economy)
            ->with('system', $system);
    }

    /**
     * Balance import event create endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doCreate(Request $request, $communityId, $economyId, $systemId) {
        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
        ]);

        // Get the community, find the economy and system
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);

        // Create the balance import event
        $event = $system->events()->create([
            'name' => $request->input('name'),
        ]);

        // Redirect to the show view after creation
        return redirect()
            ->route('community.economy.balanceimport.change.index', [
                'communityId' => $communityId,
                'economyId' => $economy->id,
                'systemId' => $system->id,
                'eventId' => $event->id,
            ])
            ->with('success', __('pages.balanceImportEvent.created'));
    }

    /**
     * Show a balance import event.
     *
     * @return Response
     */
    public function show($communityId, $economyId, $systemId, $eventId) {
        // Get the community, economy, find the system and event
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);

        return view('community.economy.balanceimport.event.show')
            ->with('economy', $economy)
            ->with('system', $system)
            ->with('event', $event);
    }

    /**
     * Edit a balance import event.
     *
     * @return Response
     */
    public function edit($communityId, $economyId, $systemId, $eventId) {
        // Get the community, economy, find the system and event
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);

        return view('community.economy.balanceimport.event.edit')
            ->with('economy', $economy)
            ->with('system', $system)
            ->with('event', $event);
    }

    /**
     * Balance import event update endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doEdit(Request $request, $communityId, $economyId, $systemId, $eventId) {
        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
        ]);

        // Get the community, economy, find the system and event
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);

        // Update the properties
        $event->name = $request->input('name');
        $event->save();

        // Redirect to the show view
        return redirect()
            ->route('community.economy.balanceimport.event.show', [
                'communityId' => $communityId,
                'economyId' => $economy->id,
                'systemId' => $system->id,
                'eventId' => $event->id,
            ])
            ->with('success', __('pages.balanceImportEvent.changed'));
    }

    /**
     * Page for confirming the deletion of balance import event.
     *
     * @return Response
     */
    public function delete($communityId, $economyId, $systemId, $eventId) {
        // Get the community, economy, find the system and event
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);

        // Do not allow deleting if there's any change
        if($event->changes()->limit(1)->count() > 0)
            return redirect()
                ->route('community.economy.balanceimport.event.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'systemId' => $system->id,
                    'eventId' => $event->id,
                ])
                ->with('error', __('pages.balanceImportEvent.cannotDeleteHasChanges'));

        return view('community.economy.balanceimport.event.delete')
            ->with('economy', $economy)
            ->with('system', $system)
            ->with('event', $event);
    }

    /**
     * Delete the balance import event.
     *
     * @return Response
     */
    public function doDelete(Request $request, $communityId, $economyId, $systemId, $eventId) {
        // Get the community, economy, find the system and event
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);

        // Do not allow deleting if there's any change
        if($event->changes()->limit(1)->count() > 0)
            return redirect()
                ->route('community.economy.balanceimport.event.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'systemId' => $system->id,
                    'eventId' => $event->id,
                ])
                ->with('error', __('pages.balanceImportEvent.cannotDeleteHasChanges'));

        // Delete the event
        $event->delete();

        // Redirect to the index page after deleting
        return redirect()
            ->route('community.economy.balanceimport.event.index', [
                'communityId' => $communityId,
                'economyId' => $economy->id,
                'systemId' => $system->id,
            ])
            ->with('success', __('pages.balanceImportEvent.deleted'));
    }

    /**
     * Page to send a balance update mail with.
     *
     * @return Response
     */
    public function mailBalance($communityId, $economyId, $systemId, $eventId) {
        // Get the community, economy, find the system
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);

        return view('community.economy.balanceimport.event.mailBalance')
            ->with('economy', $economy)
            ->with('system', $system)
            ->with('event', $event);
    }

    /**
     * Do send balance update mail.
     *
     * @return Response
     */
    public function doMailBalance(Request $request, $communityId, $economyId, $systemId, $eventId) {
        // Get the community, economy, find the system
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);

        // Validate
        $this->validate($request, [
            'message' => 'nullable|string',
            'invite_to_bar' => 'integer',
            'confirm_send_mail' => 'accepted',
        ]);

        // Read input fields
        $mail_unregistered_users = is_checked($request->input('mail_unregistered_users'));
        $mail_non_joined_users = is_checked($request->input('mail_non_joined_users'));
        $mail_joined_users = is_checked($request->input('mail_joined_users'));
        $message = $request->input('message');
        $invite_to_bar_id = (int) $request->input('invite_to_bar');
        $invite_to_bar = $invite_to_bar_id != 0 ? $community->bars()->findOrFail($invite_to_bar_id) : null;

        // Walk through all changes in this event
        $changes = $event->changes()->approved()->get();
        foreach($changes as $change) {
            // TODO: actually filter users by alias
            $alias = $change->alias;

            // Get balance, skip if zero
            $balance = $change->balance;

            // Find mutation/wallet used for change if there is any, get balance
            $mutation = $change->mutation;
            $wallet = null;
            if($mutation != null) {
                $wallet_mutation = $mutation->dependOn;
                if($wallet_mutation != null) {
                    $mutationable = $wallet_mutation->mutationable;
                    if($mutationable instanceof MutationWallet)
                        $wallet = $mutationable->wallet;
                }
            }
            if($wallet != null)
                $balance = $wallet->balance;

            // If user has zero balance, ignore
            // TODO: do continue if balance is now zero but is changed
            if($balance == null || $balance == 0)
               continue;

            // Create the mailable for the change, send the mailable
            Mail::send(new Update(
                $alias->toEmailRecipient(),
                $change,
                $message,
                $invite_to_bar,
                $mutation,
                $wallet
            ));
        }

        // Redirect to the index page after deleting
        return redirect()
            ->route('community.economy.balanceimport.change.index', [
                'communityId' => $communityId,
                'economyId' => $economy->id,
                'systemId' => $system->id,
                'eventId' => $event->id,
            ])
            ->with('success', __('pages.balanceImportMailBalance.sentBalanceUpdateEmail'));
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
