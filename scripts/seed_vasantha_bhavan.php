<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database\DB;

echo "==================================================\n";
echo "  SEEDING CHENNAI VASANTHA BHAVAN HOTEL MENU      \n";
echo "==================================================\n\n";

$db = DB::getInstance();

// 1. Ensure bill_requested column exists in tables
try {
    $db->exec("ALTER TABLE tables ADD COLUMN bill_requested INTEGER DEFAULT 0");
    echo " -> Added bill_requested column to tables table.\n";
} catch (\Exception $e) {
    // Column already exists
}

// 2. Clear old dishes
$db->exec("DELETE FROM dishes");
echo " -> Cleared existing dishes table.\n";

// 3. Define Chennai Vasantha Bhavan Pure Veg Menu Items
$dishes = [
    // ------------------- STARTERS & SOUPS -------------------
    [
        'name' => 'Gobi 65 (Crispy Veg Starter)',
        'description' => 'Crispy deep-fried cauliflower florets marinated with authentic South Indian spices and fresh curry leaves.',
        'price' => 160.00,
        'category' => 'Starters & Soups',
        'image_url' => 'https://images.unsplash.com/photo-1626777552726-4a6b54c97e46?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Mushroom Pepper Fry',
        'description' => 'Fresh button mushrooms sautéed with coarsely crushed black pepper, shallots, and fragrant curry leaves.',
        'price' => 180.00,
        'category' => 'Starters & Soups',
        'image_url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Paneer 65 (Spicy Cottage Cheese)',
        'description' => 'Tender cottage cheese cubes tossed in spicy red chili garlic glaze with green chilies.',
        'price' => 190.00,
        'category' => 'Starters & Soups',
        'image_url' => 'https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Vasantha Special Veg Soup',
        'description' => 'Nourishing hot vegetable soup brewed with fresh herbs, coriander stems, and mild black pepper.',
        'price' => 110.00,
        'category' => 'Starters & Soups',
        'image_url' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Crispy Vegetable Cutlet (2 Pcs)',
        'description' => 'Golden fried spiced potato and garden vegetable croquettes served with tangy mint chutney.',
        'price' => 120.00,
        'category' => 'Starters & Soups',
        'image_url' => 'https://images.unsplash.com/photo-1599487488170-d11ec9c172f0?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],

    // ------------------- BREAKFAST & TIFFIN SPECIALTIES -------------------
    [
        'name' => 'Vasantha Bhavan Special Ghee Roast Dosa',
        'description' => 'Golden crispy paper-thin Dosa roasted in pure cow ghee, served with 3 coconut chutneys and hot piping Sambar.',
        'price' => 140.00,
        'category' => 'Breakfast & Tiffin',
        'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Podi Idli (Ghee Tossed)',
        'description' => 'Steamed fluffy rice cakes tossed in signature aromatic spicy Gunpowder (Podi) and melted cow ghee.',
        'price' => 110.00,
        'category' => 'Breakfast & Tiffin',
        'image_url' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Vasantha Mini Tiffin Combo',
        'description' => 'Grand breakfast platter featuring 1 Mini Masala Dosa, 2 Mini Idlis, 1 Medu Vada, Rava Kesari, and Sambar.',
        'price' => 170.00,
        'category' => 'Breakfast & Tiffin',
        'image_url' => 'https://images.unsplash.com/photo-1610192244261-3f33de3f55e4?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Rava Onion Masala Dosa',
        'description' => 'Crispy lacy semolina crepe studded with finely chopped shallots, cashews, and spiced potato filling.',
        'price' => 150.00,
        'category' => 'Breakfast & Tiffin',
        'image_url' => 'https://images.unsplash.com/photo-1626777552726-4a6b54c97e46?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Medu Vada (2 Pcs)',
        'description' => 'Traditional deep-fried crispy lentil donut fritters served with coconut chutney and hot Sambar.',
        'price' => 80.00,
        'category' => 'Breakfast & Tiffin',
        'image_url' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Poori Masala (3 Pcs)',
        'description' => 'Puffed golden whole wheat deep-fried breads served with authentic spiced potato onion masala curry.',
        'price' => 120.00,
        'category' => 'Breakfast & Tiffin',
        'image_url' => 'https://images.unsplash.com/photo-1626777552726-4a6b54c97e46?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],

    // ------------------- SOUTH INDIAN MAINS & MEALS -------------------
    [
        'name' => 'Vasantha Bhavan Special South Indian Thali / Meals',
        'description' => 'Royal South Indian thali featuring Sweet, Ponni Rice, Sambar, Rasam, Kara Kuzhambu, Poriyal, Kootu, Appalam, Curd & Pickle.',
        'price' => 210.00,
        'category' => 'South Indian Mains',
        'image_url' => 'https://images.unsplash.com/photo-1610192244261-3f33de3f55e4?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Mini Meals Platter',
        'description' => 'Compact meal platter served with Sambar Rice, Curd Rice, Special Variety Rice of the day, Poriyal, and Appalam.',
        'price' => 150.00,
        'category' => 'South Indian Mains',
        'image_url' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Special Veg Biryani with Raitha',
        'description' => 'Fragrant Seeraga Samba rice dum-cooked with vegetables, fresh mint & whole spices, served with onion cucumber raitha.',
        'price' => 180.00,
        'category' => 'South Indian Mains',
        'image_url' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Paneer Butter Masala',
        'description' => 'Fresh cottage cheese cubes simmered in a creamy, velvety tomato and cashew nut rich butter gravy.',
        'price' => 210.00,
        'category' => 'South Indian Mains',
        'image_url' => 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Mushroom Kadai Masala',
        'description' => 'Button mushrooms and crunchy diced capsicum cooked in roasted kadai spice coriander tomato gravy.',
        'price' => 200.00,
        'category' => 'South Indian Mains',
        'image_url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],

    // ------------------- BREADS & RICE DISHES -------------------
    [
        'name' => 'Special Ghee Rice with Cashews',
        'description' => 'Aromatic Basmati rice cooked in pure desi ghee, garnished with fried golden cashews, raisins, and mint.',
        'price' => 160.00,
        'category' => 'Breads & Rice',
        'image_url' => 'https://images.unsplash.com/photo-1596560548464-f010549b84d7?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Chennai Special Curd Rice',
        'description' => 'Soothing chilled creamy curd rice tempered with mustard seeds, curry leaves, ginger, green chilies and fresh pomegranate.',
        'price' => 110.00,
        'category' => 'Breads & Rice',
        'image_url' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Ghee Sambar Rice',
        'description' => 'Authentic Tamil Nadu Sambar rice cooked with garden vegetables and topped with a generous dollop of pure ghee.',
        'price' => 120.00,
        'category' => 'Breads & Rice',
        'image_url' => 'https://images.unsplash.com/photo-1610192244261-3f33de3f55e4?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Malabar Parotta with Salna (2 Pcs)',
        'description' => 'Flaky multi-layered South Indian flatbreads served with aromatic spicy vegetable salna curry.',
        'price' => 130.00,
        'category' => 'Breads & Rice',
        'image_url' => 'https://images.unsplash.com/photo-1588166524941-3bf61a9c41db?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Butter Naan / Garlic Naan',
        'description' => 'Tandoor baked soft Indian flatbread brushed liberally with fresh melted butter or garlic herbs.',
        'price' => 70.00,
        'category' => 'Breads & Rice',
        'image_url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],

    // ------------------- DESSERTS & BEVERAGES -------------------
    [
        'name' => 'Authentic Kumbakonam Filter Coffee',
        'description' => 'Freshly brewed strong chicory coffee decoction with frothed hot milk served in traditional South Indian brass dabba-tumbler.',
        'price' => 50.00,
        'category' => 'Desserts & Beverages',
        'image_url' => 'https://images.unsplash.com/photo-1534778101976-62847782c213?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Special Elaneer Payasam',
        'description' => 'Exquisite chilled dessert made with sweet tender coconut pulp, coconut water, cardamom, and thick cardamom milk.',
        'price' => 130.00,
        'category' => 'Desserts & Beverages',
        'image_url' => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Hot Gulab Jamun (2 Pcs)',
        'description' => 'Soft golden fried milk solid dumplings soaked in cardamom and saffron sugar syrup.',
        'price' => 90.00,
        'category' => 'Desserts & Beverages',
        'image_url' => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Chilled Badam Milk',
        'description' => 'Thick refreshing chilled milk flavored with ground almond paste, saffron strands, and slivered pistachios.',
        'price' => 100.00,
        'category' => 'Desserts & Beverages',
        'image_url' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ],
    [
        'name' => 'Fresh Rose Milk',
        'description' => 'Classic chilled summer cooler made with whole milk, natural Damask rose syrup, and swollen basil seeds (Sabja).',
        'price' => 80.00,
        'category' => 'Desserts & Beverages',
        'image_url' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=400&q=80',
        'is_available' => 1
    ]
];

$stmt = $db->prepare("INSERT INTO dishes (name, description, price, category, image_url, is_available) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($dishes as $d) {
    $stmt->execute([
        $d['name'],
        $d['description'],
        $d['price'],
        $d['category'],
        $d['image_url'],
        $d['is_available']
    ]);
}

echo " -> Inserted " . count($dishes) . " Chennai Vasantha Bhavan menu items successfully!\n";
echo "\n==================================================\n";
echo "  MENU SEEDING COMPLETED SUCCESSFULLY!            \n";
echo "==================================================\n";
