<?php
require_once __DIR__ . '/../Backend/controller/productController.php';

// Pixabay Unrestricted/Sample URLs
$replacements = [
    'lotion.jpg' => 'https://cdn.pixabay.com/photo/2016/11/29/03/42/cosmetics-1867131_1280.jpg',
    'cream.jpg' => 'https://cdn.pixabay.com/photo/2017/09/20/14/53/spa-2768783_1280.jpg',
    'oil.jpg' => 'https://cdn.pixabay.com/photo/2016/10/26/19/02/essential-oils-1772242_1280.jpg',
    'soap.jpg' => 'https://cdn.pixabay.com/photo/2017/05/22/07/39/soap-2333412_1280.jpg'
];

echo "Restoring product images...\n";

$products = $productController->listAll();

foreach ($products as $p) {
    if (isset($replacements[$p['image']])) {
        $url = $replacements[$p['image']];
        echo "Updating image for {$p['name']} from $url...\n";

        $data = [
            'name' => $p['name'],
            'description' => $p['description'],
            'ingredients' => $p['ingredients'],
            'price' => $p['price'],
            'category' => $p['category'],
            'is_featured' => $p['is_featured'],
            'image_url' => $url
        ];

        $result = $productController->update($p['id'], $data, null);
        echo "Result: " . ($result['success'] ? "Success" : "Failed: " . $result['message']) . "\n";
    }
}

// Manually download hero image
echo "Downloading hero image...\n";
$heroUrl = 'https://cdn.pixabay.com/photo/2017/08/06/12/06/leaves-2591942_1280.jpg';
$content = @file_get_contents($heroUrl);
if ($content) {
    file_put_contents(__DIR__ . '/../Frontend/images/hero.jpg', $content);
    echo "Hero image restored.\n";
} else {
    echo "Failed to download hero image.\n";
}

echo "Restoration complete.\n";
?>