<?php

namespace VklComponents\VklTable;

use Carbon\Carbon;

/**
 * Helper functions for vklComponents/vklTable/VklTableBuilder.php
 * @package vklComponents\vklTable
 */
class VklTableHelper
{
    /**
     * Get value in requested format.
     *
     * @param mixed $value
     * @param string $requestedFormat
     *
     * @return mixed
     */
    public static function convertValue($value, string $requestedFormat)
    {
        switch ($requestedFormat) {
            case 'app_datetime':
                return self::convertBaseDateTimeToAppDateTime($value);
                break;
            case 'app_time':
                return self::convertBaseDateTimeToAppTime($value);
                break;
            case 'app_date':
                return self::convertBaseDateTimeToAppDate($value);
                break;
            default:
                return $value;
        }
    }

    /**
     * Convert base dateTime string to app datetime format.
     *
     * @param string $baseDateTime
     *
     * @return string
     */
    public static function convertBaseDateTimeToAppDateTime(string $baseDateTime)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $baseDateTime)->format('j/n/Y H:i');
    }

    /**
     * Convert base dateTime string to app time format.
     *
     * @param string $baseDateTime
     *
     * @return string
     */
    public static function convertBaseDateTimeToAppTime(string $baseDateTime)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $baseDateTime)->format('H:i');
    }

    /**
     * Convert base dateTime string to app date format.
     *
     * @param string $baseDateTime
     *
     * @return string
     */
    public static function convertBaseDateTimeToAppDate(string $baseDateTime)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $baseDateTime)->format('j/n/Y');
    }

    /**
     * Get response with generated excel file.
     *
     * @param array $data - array of records (the order of columns in the records should be the same as in the headers).
     * @param array $columnHeaders - array of column headers that should be in export
     * @param string|null $fileName - excel file name
     * @param string $csvDelimiter - delimiter symbol for excel export csv file
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public static function getExcelResponse(array $data, array $columnHeaders, string $fileName = null, string $csvDelimiter = ',')
    {
        $fileName = $fileName ?? date('m-d-Y_h-ia');

        // add file extension
        $fileName .= '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $fileName,
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $closure = function() use ($data, $columnHeaders, $csvDelimiter) {
            $file = fopen('php://output', 'w');

            // put column titles
            fputcsv($file, $columnHeaders, $csvDelimiter);

            foreach ($data as $record) {
                // put record
                fputcsv($file, $record, $csvDelimiter);
            }

            fclose($file);
        };

        return response()->stream($closure, 200, $headers);
    }

    /**
     * Get record in requested export format.
     * Return only visible columns in requested formats.
     *
     * @param array $record
     * @param array $visibleColumns - array of column names that should be in export, in the order that should be in the export
     * @param array $columnFormats - column formats in array (column => format)
     * @param array $customExportColumns - columns with custom export column format in closure
     * @return array
     */
    public static function getRecordInExportFormat(array $record, array $visibleColumns, array $columnFormats = [], array $customExportColumns = [])
    {
        $exportRecord = [];

        foreach ($visibleColumns as $columnName) {
            $columnValue = $record[$columnName] ?? null;

            // check if export column is custom
            if (array_key_exists($columnName, $customExportColumns)) {
                $columnValue = $customExportColumns[$columnName]($record);
                // check if export column has format
            } elseif (array_key_exists($columnName, $columnFormats)) {
                $columnValue = self::convertValue($columnValue, $columnFormats[$columnName]);
            }

            array_push($exportRecord, $columnValue);
        }

        return $exportRecord;
    }
}