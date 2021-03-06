<?php

use \Maatwebsite\Excel\Excel as ExcelType;

return [

    /*
    |--------------------------------------------------------------------------
    | Bar properties.
    |--------------------------------------------------------------------------
    */

    /**
     * Multiple quick buy actions within this number of seconds will be merged
     * into a single transaction if possible.
     */
    'quick_buy_merge_timeout' => 60 * 2.5,

    /**
     * The number of seconds to show recent product transactions for on bar
     * pages.
     *
     * This is done for social control, to show other users for a short period
     * of time what is bought.
     */
    'bar_recent_product_transaction_period' => 60 * 60,

    /**
     * Spreadsheet export types.
     */
    'spreadsheet_export_types' => [
        ['type' => ExcelType::XLSX, 'name' => 'Excel spreadsheet (.xlsx)', 'extension' => 'xlsx'],
        ['type' => ExcelType::XLS, 'name' => 'Excel spreadsheet (1997-2003) (.xls)', 'extension' => 'xls'],
        ['type' => ExcelType::CSV, 'name' => 'Comma Separated Values (.csv)', 'extension' => 'csv'],
        ['type' => ExcelType::ODS, 'name' => 'OpenDocument Spreadsheet (.ods)', 'extension' => 'ods'],
        ['type' => ExcelType::HTML, 'name' => 'HTML (.html)', 'extension' => 'html'],
        ['type' => ExcelType::DOMPDF, 'name' => 'PDF (.pdf)', 'extension' => 'pdf'],
    ],
];
