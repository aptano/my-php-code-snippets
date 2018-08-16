<?php

class Adworx_Export_Csv
{
    public function __construct()
    {

    }

    public function exportCSV($headings=false, $rows=false, $filename=false, $delimiter=';')
    {
        # Ensure that we have data to be able to export the CSV
        if ((!empty($headings)) AND (!empty($rows)))
        {
            # modify the name somewhat
            $name = ($filename !== false) ? $filename . ".csv" : "export.csv";

            # Set the headers we need for this to work
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $name);

            # Start the ouput
            $output = fopen('php://output', 'w');

            # Create the headers
            fputcsv($output, $headings);

            # Then loop through the rows
            foreach($rows as $row)
            {
                # Add the rows to the body
                fputcsv($output, $row);
            }

            exit();

            fclose($output);
        }

        # Default to a failure
        return false;

        //$file = fopen("contacts.csv","w");
    }
}

