<?php


header('Content-Type: application/json'); 

function csvToArray($csvFile)
{

    $file_to_read = fopen($csvFile, 'r');
    while (!feof($file_to_read)) {
        $lines[] = fgetcsv($file_to_read, 1000, ',');
    }
    fclose($file_to_read);
    return $lines;
}
 

// LOAD XPATH
function Load_xpath($url)
{
    $site_url = $url; 

    $html = file_get_contents($site_url);
    
    $e = [];
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    return new DomXPath($dom);

}

// GET COUNTRY LINKS
function get_links()
{

    $country_link_csv = fopen('./country_link.csv', 'w'); 
    $xpath = Load_xpath('https://www.numbeo.com/cost-of-living/');
    $a = "";

    // $table_selector = './/div//*[contains(concat(" ",normalize-space(@class)," ")," related_links ")]';
    $table_selector = './/div//*[contains(concat(" ",normalize-space(@class)," ")," related_links ")]//a';

    $all_elements = $xpath->query($table_selector);
    $a .= 'Country links';
    foreach ($all_elements as $key => $value ) 
    { 

        $a .= "\n";
        $a .= $value->getAttribute('href');
        $a .= ",";
    }
    
    fwrite($country_link_csv, $a);
    fclose($country_link_csv);
}

function get_links_property ()
{


    // $csvFile = './country_link.csv';
    $csvFile = './country_link.csv';
    $csv = csvToArray($csvFile); 

    foreach($csv as $each_country_link)
    {
        
        if($each_country_link[0] == 'Country links' ) 
        {
            continue;
        }else{

            // specific country url
            $url = 'https://www.numbeo.com/cost-of-living/'.$each_country_link[0].'&displayCurrency=USD'; 
            $html = Load_xpath($url);
            $a = "";
        
            $all_data='.//table[contains(concat(" ", @class, " "), " data_wide_table")]//tr//td[1] | .//table[contains(concat(" ", @class, " "), " data_wide_table")]//tr//td[2]';
            // $price_selector = '';
            $all_data = $html->query($all_data); 
            
            foreach($all_data as $key => $value)
            {
        
                // print_r($key); 
                if($key % 2 == 0){
        
                    // items desc
                    $a .= $value->textContent;
                    $a .= ";";
        
                }else{

                    // including price
                    $a .= $value->textContent;
                    $a .= "\n";
                    
                }
            } 

            
            // save to specific country file
            $country_name = str_replace("country_result.jsp?country=", "",$each_country_link[0]);

            // for update functionality
            // if (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . "each-country-living-of-cost" . DIRECTORY_SEPARATOR . $country_name.'_cost_of_living.csv' )) 
            // {
            //     $each_country_file = fopen('./each-country-living-of-cost/'.$country_name.'_cost_of_living.csv', 'w'); 
            //     fwrite($each_country_file, $a);
            //     fclose($each_country_file);
            // } 
 
            $each_country_file = fopen('./each-country-living-of-cost/'.$country_name.'_cost_of_living.csv', 'w'); 
            fwrite($each_country_file, $a);
            fclose($each_country_file);
        }
        
    }
    
    
 

}
// get_links();
// get_links_property();


function updated_with_categories_get_links_property(){



    $csvFile = './country_link.csv';
    // $csvFile = './testing_country_links.csv';
    $csv = csvToArray($csvFile); 

    // $get_data = ('https://www.numbeo.com/cost-of-living/country_result.jsp?country=Afghanistan');
    foreach($csv as $each_country_link)
    {
        if($each_country_link[0] == 'Country links' ) 
        {
            continue;
        }else{

            // specific country url
            $url = 'https://www.numbeo.com/cost-of-living/'.$each_country_link[0].'&displayCurrency=USD'; 
            echo 'Target url ' .$url ; echo "\n";
            $html = Load_xpath($url);

            $a = "";
            $a .= "categorie;title;price";
            $a .= "\n";

            $all_data=(
                './/table[contains(concat(" ", @class, " "), " data_wide_table")]//tr//th//div |
            .//table[contains(concat(" ", @class, " "), " data_wide_table")]//tr//td[1] | 
            .//table[contains(concat(" ", @class, " "), " data_wide_table")]//tr//td[2]'
            );

            $all_data = $html->query($all_data);
            $even = true;
            $current_cat='';
            foreach($all_data as $key => $value)
            { 
                
                //categorie and even bool condition
                if ($value->nodeName == 'div'){
                    $current_cat = $value->textContent; 
                    $even = !$even;
                }
               
                if($value->nodeName =='td')
                {
                     
                    if($even){

                        if($key % 2 == 0){
                                        
                            // categorie
                            echo 'categorie is -->  .' . $current_cat;
                            $a .= $current_cat;
                            $a .= ";";
                            // item title
                            echo "\t title --> ". $value->textContent;
                            $a .= $value->textContent;
                            $a .= ";";

                        }else{


                            // price 
                            echo "\t price --> ".$value->textContent . "\n"; 
                            $a .= $value->textContent;
                            $a .= ";";
                            $a .= "\n";
                
                            
                        }

                    }
                    elseif(!$even){
                        // print_r($key); 
                        if($key % 2 == 0){

                            // price 
                            echo "\t price --> ".$value->textContent . "\n"; 
                            $a .= $value->textContent;
                            $a .= ";";
                            $a .= "\n";;
                        
                
                        }else{
                                        
                            // categorie
                            echo 'categorie is -->  .' . $current_cat;
                            $a .= $current_cat;
                            $a .= ";";
                            // title
                            echo "\t title --> ". $value->textContent; 
                            $a .= $value->textContent;
                            $a .= ";";

                            
                        }
                    }
                    echo "\n";
                };
        

            }


            // save to specific country file
            $country_name = str_replace("country_result.jsp?country=", "",$each_country_link[0]);
            $each_country_file = fopen('./each-country-with-categories-loc/'.$country_name.'_cost_of_living.csv', 'w'); 
            fwrite($each_country_file, $a);
            fclose($each_country_file);
            

        }


    }

   

}

updated_with_categories_get_links_property();