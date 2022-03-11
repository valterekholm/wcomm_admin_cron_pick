<?php

   $host = "localhost";
   $username  = "root";
   $passwd = "";
   $dbname = "wordpress";

?>

<?php

$message = "Hej. Med detta automatiska mail ber vi dig kontrollera följande slumpvis valda produkt:\n\n";

printf($message);

//to get id:s
$sql = "SELECT
p.post_type,
GROUP_CONCAT(p.ID) AS all_ids
FROM
wp_posts p
LEFT JOIN
wp_postmeta pm ON 
    pm.post_id = p.ID AND
    pm.meta_key = '_thumbnail_id'
LEFT JOIN
wp_postmeta am ON
    am.post_id = pm.meta_value AND
    am.meta_key = '_wp_attached_file'
WHERE
p.post_type = 'product' AND
p.post_status = 'publish' AND
p.post_content <> ''
GROUP BY(p.post_type)";

//Creating a connection
$conn = mysqli_connect($host, $username, $passwd, $dbname);

if($conn){
   //printf("Connection Established Successfully\n");
}else{
   printf("Connection Failed\n");
   exit;
}

$result = $conn->query($sql);

$all_ids = "";

if ($result->num_rows > 0) {
  // output data of each row
  //$row["post_type"].
  while($row = $result->fetch_assoc()) {
    //echo $row["all_ids"] . "<br>";
    $all_ids = $row["all_ids"];
  }
} else {
  printf("0 results");
}
//$conn->close();

$ids_array = explode(",",$all_ids);
$len = count($ids_array);

//echo "Array length: $len<br>";

$random_index = rand(0,$len-1);

//echo "Random index: $random_index<br>";

$random_id = $ids_array[$random_index];

printf("Random id: $random_id\n");

//get a "random" product:

$sql2 = "SELECT 
p.ID,
p.post_title,
`post_content`,
`post_excerpt`,
t.name AS product_category,
t.term_id AS product_id,
t.slug AS product_slug,
tt.term_taxonomy_id AS tt_term_taxonomia,
tr.term_taxonomy_id AS tr_term_taxonomia,
MAX(CASE WHEN pm1.meta_key = '_price' then pm1.meta_value ELSE NULL END) as price,
MAX(CASE WHEN pm1.meta_key = '_regular_price' then pm1.meta_value ELSE NULL END) as regular_price,
MAX(CASE WHEN pm1.meta_key = '_sale_price' then pm1.meta_value ELSE NULL END) as sale_price,
MAX(CASE WHEN pm1.meta_key = '_sku' then pm1.meta_value ELSE NULL END) as sku,
MAX(CASE WHEN pm1.meta_key = '_stock' then pm1.meta_value ELSE NULL END) as stock,
am.meta_value AS thumbnail
FROM wp_posts p 
LEFT JOIN wp_postmeta pm1 ON pm1.post_id = p.ID AND pm1.meta_key = '_thumbnail_id'
LEFT JOIN wp_term_relationships AS tr ON tr.object_id = p.ID
LEFT JOIN wp_postmeta am ON
  am.post_id = pm1.meta_value AND
  am.meta_key = '_wp_attached_file'
JOIN wp_term_taxonomy AS tt ON tt.taxonomy = 'product_cat' AND tt.term_taxonomy_id = tr.term_taxonomy_id 
JOIN wp_terms AS t ON t.term_id = tt.term_id
WHERE p.post_type in('product', 'product_variation') AND p.post_status = 'publish' AND p.post_content <> '' AND ID = $random_id
GROUP BY p.ID,p.post_title";



$result = $conn->query($sql2);

$config['base_url'] = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$config['base_url'] .= "://".$_SERVER['HTTP_HOST'];
$config['base_url'] .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);


$base_url = $config['base_url'];

//echo $base_url . "<br>";

if ($result->num_rows > 0) {
  //output data of each row
  //$row["post_type"].
  while($row = $result->fetch_assoc()) {
    $img = $row["thumbnail"];
    $img_url = $base_url . "wp-content/uploads/$img";
    $title = $row["post_title"];
    $stock = $row["stock"];
    $price = $row["price"];
    $regular_price = $row["regular_price"];
    $category = $row["product_category"];
    $slug = $row["product_slug"];
    printf("$title   ");
    printf("kategori: $category   ");
    printf("antal i lager: $stock   ");
    printf("pris: $price, normalpris: $regular_prize   ");
    if(empty($img)){
        printf("Ingen bild fanns\n");
    }
    else{
        printf("Image: $img   ");
        //echo "<img src='$img_url' width=300>" . "<br>";
        printf("<a href='$img_url' target='_blank'>Bild</a>   ");
    }
    $product_url = $base_url . "product/$slug/";
    printf("Adress till produkt: $product_url");

  }
} else {
  printf("0 results   ");
}
$conn->close();

?>
