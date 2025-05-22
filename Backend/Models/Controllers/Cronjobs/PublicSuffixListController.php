<?php 

namespace Backend\Controllers\Cronjobs;

class PublicSuffixListController
{
    public function update()
    {
        $output = file_get_contents("https://publicsuffix.org/list/public_suffix_list.dat");

        if ($output)
        {
            $handler = fopen(base_path("storage/domains/suffix.dat"), "w");
            fwrite($handler, $output);
            fclose($handler);
        }
    }
}