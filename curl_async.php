<?php

/*
 Function curl_async
 Written by Karl Babcock
 
 An easy PHP function that asynchronously uses cURL to call an array of URLs.
 
 This function will accept input of an array of URLs
 It will return the body of those URLs as an array in the same order as input.
*/

//We need an array of URLs to give the function.  Generate this any way you wish.
$urls = array('https://www.karlbabcock.com/relay.php?message=0','https://www.karlbabcock.com/relay.php?message=1','https://www.karlbabcock.com/relay.php?message=2');
// Array built.

//Choose the cURL header options.
function build_curl_opt($url){
	$options = array(CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_SSL_VERIFYHOST => false,
	CURLOPT_SSL_VERIFYPEER => false
	);
	return $options;
}

//cURL asynchronous function. Takes array of URL's and fethces them asynchronous, returning results in the same array order as received.
function curl_async($urls) {
  $i = 0;
  foreach($urls as $url){
    ${"ch_{$i}"} = curl_init();
    curl_setopt_array(${"ch_{$i}"}, build_curl_opt($url));
      $i++;
  }

  $mh = curl_multi_init();

  $i_mh = 0;
  do {
      curl_multi_add_handle($mh, ${"ch_{$i_mh}"});
    $i_mh++;
  } while ($i_mh < $i);
    
    $running = null;
    do {
      curl_multi_exec($mh, $running);
    } while ($running);

  $i_mh = 0;
  do {
      curl_multi_remove_handle($mh, ${"ch_{$i_mh}"});
    $i_mh++;
  } while ($i_mh < $i);

  curl_multi_close($mh);
    
  // all of our requests are done, we can now access the results
  $results = [];
  $i_mh = 0;
  do {
      $results[] = curl_multi_getcontent(${"ch_{$i_mh}"});
    $i_mh++;
  } while ($i_mh < $i);

  return $results;
}

//Run the Async job
$curl_job1 = curl_async($urls);

//Print the results array!
print_r($curl_job1);
