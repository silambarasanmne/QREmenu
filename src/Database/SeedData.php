<?php

namespace App\Database;

class SeedData
{
    public static function getTables(): array
    {
        return [
            'Table 1',
            'Table 2',
            'Table 3',
            'Table 4',
            'Table 5',
            'Table 6',
            'Table 7',
            'Table 8'
        ];
    }

    public static function getDishes(): array
    {
        return [
            // ------------------- 1. SOUPS -------------------
            [
                'name' => 'Pepper Soup',
                'description' => 'Aromatic and spicy South Indian pepper broth infused with garlic, cumin, and fresh herbs.',
                'price' => 50.00,
                'category' => 'Soups',
                'image_url' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Tomato Soup',
                'description' => 'Rich and velvety roasted tomato soup served with buttered croutons.',
                'price' => 50.00,
                'category' => 'Soups',
                'image_url' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Veg Clear Soup',
                'description' => 'Light and refreshing soup made with diced farm-fresh vegetables and mild herbs.',
                'price' => 50.00,
                'category' => 'Soups',
                'image_url' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Cream of Veg Soup',
                'description' => 'Creamy soup blended with fresh garden vegetables and crushed black pepper.',
                'price' => 50.00,
                'category' => 'Soups',
                'image_url' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Sweet Corn Soup',
                'description' => 'Classic Indo-Chinese soup loaded with juicy sweet corn kernels and subtle spices.',
                'price' => 60.00,
                'category' => 'Soups',
                'image_url' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],

            // ------------------- 2. NOODLES -------------------
            [
                'name' => 'Veg Noodles',
                'description' => 'Stir-fried noodles tossed with shredded carrots, cabbage, capsicum, and oriental sauces.',
                'price' => 200.00,
                'category' => 'Noodles',
                'image_url' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Mushroom Noodles',
                'description' => 'Wok-tossed noodles cooked with juicy tender button mushrooms and savory Chinese spices.',
                'price' => 210.00,
                'category' => 'Noodles',
                'image_url' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Gobi Noodles',
                'description' => 'Crispy fried cauliflower florets tossed with hakka noodles and fresh spring onions.',
                'price' => 210.00,
                'category' => 'Noodles',
                'image_url' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Schezwan Noodles',
                'description' => 'Spicy wok-fried noodles tossed in fiery homemade Schezwan sauce and vegetables.',
                'price' => 210.00,
                'category' => 'Noodles',
                'image_url' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Paneer Noodles',
                'description' => 'Stir-fried noodles loaded with golden paneer cubes and crunchy garden vegetables.',
                'price' => 210.00,
                'category' => 'Noodles',
                'image_url' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],

            // ------------------- 3. SPECIAL DOSA -------------------
            [
                'name' => 'Egg Oothapam',
                'description' => 'Thick savory rice crepe topped with beaten spiced egg, onions, and green chilies.',
                'price' => 100.00,
                'category' => 'Special Dosa',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Mushroom Masala Dosa',
                'description' => 'Crispy dosa filled with a spicy sautéed mushroom and onion masala.',
                'price' => 120.00,
                'category' => 'Special Dosa',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Ghee Masala Dosa',
                'description' => 'Golden paper-thin dosa roasted in pure cow ghee, stuffed with potato masala.',
                'price' => 120.00,
                'category' => 'Special Dosa',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Veg Masala Dosa',
                'description' => 'Crispy rice crepe stuffed with a rich mix of spiced garden vegetables.',
                'price' => 120.00,
                'category' => 'Special Dosa',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Paneer Masala Dosa',
                'description' => 'Golden crisp dosa stuffed with grated spiced cottage cheese and potato mash.',
                'price' => 130.00,
                'category' => 'Special Dosa',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],

            // ------------------- 4. BREAKFAST & TIFFIN -------------------
            [
                'name' => 'Idly (2 Pcs)',
                'description' => 'Steamed fluffy soft rice cakes served with sambar and coconut chutneys.',
                'price' => 40.00,
                'category' => 'Breakfast & Tiffin',
                'image_url' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Sambar Idly (2 Pcs)',
                'description' => 'Steamed soft idlis fully immersed in piping hot vegetable sambar topped with ghee.',
                'price' => 60.00,
                'category' => 'Breakfast & Tiffin',
                'image_url' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Vada (2 Pcs)',
                'description' => 'Crispy deep-fried savory black gram donuts seasoned with peppercorns and curry leaves.',
                'price' => 30.00,
                'category' => 'Breakfast & Tiffin',
                'image_url' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Sambar Vada (2 Pcs)',
                'description' => 'Crispy vadas soaked in hot spiced lentil sambar.',
                'price' => 40.00,
                'category' => 'Breakfast & Tiffin',
                'image_url' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Curd Vada (2 Pcs)',
                'description' => 'Crispy vadas soaked in sweetened chilled seasoned yogurt topped with mustard and coriander.',
                'price' => 45.00,
                'category' => 'Breakfast & Tiffin',
                'image_url' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Ven Pongal',
                'description' => 'Traditional rice and yellow moong dal porridge tempered with ghee, cumin, black pepper, and cashews.',
                'price' => 75.00,
                'category' => 'Breakfast & Tiffin',
                'image_url' => 'https://images.unsplash.com/photo-1610192244261-3f33de3f55e4?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Poori Masala (2 Pcs)',
                'description' => 'Puffed golden whole wheat deep-fried breads served with savory potato-onion masala.',
                'price' => 75.00,
                'category' => 'Breakfast & Tiffin',
                'image_url' => 'https://images.unsplash.com/photo-1626777552726-4a6b54c97e46?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Idiyappam with Kuruma Vadacurry',
                'description' => 'Steamed rice string hoppers served with rich aromatic spicy kuruma and vadacurry.',
                'price' => 75.00,
                'category' => 'Breakfast & Tiffin',
                'image_url' => 'https://images.unsplash.com/photo-1610192244261-3f33de3f55e4?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Mini Idly (14 Pcs)',
                'description' => 'Bite-sized button idlis soaked in ghee sambar and coriander.',
                'price' => 80.00,
                'category' => 'Breakfast & Tiffin',
                'image_url' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Combo Mini Tiffin',
                'description' => 'Grand breakfast combo featuring 1 Idly, 1 Vada, 1 Mini Masala Dosa, Pongal, Sweet, and Filter Coffee.',
                'price' => 120.00,
                'category' => 'Breakfast & Tiffin',
                'image_url' => 'https://images.unsplash.com/photo-1610192244261-3f33de3f55e4?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],

            // ------------------- 5. DOSAI VARIETIES -------------------
            [
                'name' => 'Plain Dosa',
                'description' => 'Classic thin golden crispy fermented rice and lentil crepe.',
                'price' => 75.00,
                'category' => 'Dosai Varieties',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Masala Dosa',
                'description' => 'Traditional crisp dosa filled with spiced potato mash, served with sambar and chutneys.',
                'price' => 120.00,
                'category' => 'Dosai Varieties',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Onion Dosa',
                'description' => 'Crisp dosa topped with finely chopped onions and fresh green chilies.',
                'price' => 110.00,
                'category' => 'Dosai Varieties',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Podi Dosa',
                'description' => 'Crisp dosa smeared with fragrant South Indian spice powder (gunpowder) and ghee.',
                'price' => 100.00,
                'category' => 'Dosai Varieties',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Ghee Dosa',
                'description' => 'Rich and crisp dosa liberally brushed with aromatic desi ghee.',
                'price' => 120.00,
                'category' => 'Dosai Varieties',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Rava Dosa',
                'description' => 'Lacy, net-like crisp crepe made from semolina, cumin seeds, and crushed pepper.',
                'price' => 80.00,
                'category' => 'Dosai Varieties',
                'image_url' => 'https://images.unsplash.com/photo-1626777552726-4a6b54c97e46?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Onion Rava Dosai',
                'description' => 'Crispy lacy semolina crepe studded with finely chopped onions and cashews.',
                'price' => 120.00,
                'category' => 'Dosai Varieties',
                'image_url' => 'https://images.unsplash.com/photo-1626777552726-4a6b54c97e46?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Ghee Rava Dosai',
                'description' => 'Crunchy semolina crepe roasted in pure golden cow ghee.',
                'price' => 120.00,
                'category' => 'Dosai Varieties',
                'image_url' => 'https://images.unsplash.com/photo-1626777552726-4a6b54c97e46?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Onion Oothappam',
                'description' => 'Thick and soft pancake topped with caramelized onions and green chilies.',
                'price' => 110.00,
                'category' => 'Dosai Varieties',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Tomato Oothappam',
                'description' => 'Soft thick rice pancake loaded with fresh chopped juicy tomatoes and herbs.',
                'price' => 95.00,
                'category' => 'Dosai Varieties',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],

            // ------------------- 6. GRAVY & CURRIES -------------------
            [
                'name' => 'Dhall Fry',
                'description' => 'Yellow lentils tempered with cumin seeds, garlic, onions, tomatoes, and butter.',
                'price' => 180.00,
                'category' => 'Gravy & Curries',
                'image_url' => 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Paneer Butter Masala',
                'description' => 'Soft paneer cubes simmered in a rich tomato, butter, and cream cashew gravy.',
                'price' => 210.00,
                'category' => 'Gravy & Curries',
                'image_url' => 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Mushroom Masala',
                'description' => 'Button mushrooms cooked in spiced onion tomato gravy with North Indian spices.',
                'price' => 220.00,
                'category' => 'Gravy & Curries',
                'image_url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Alu Gobi Masala',
                'description' => 'Potatoes and cauliflower florets sautéed in thick spiced onion tomato gravy.',
                'price' => 210.00,
                'category' => 'Gravy & Curries',
                'image_url' => 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Chenna Masala',
                'description' => 'Tender chickpeas simmered in a tangy Punjabi spiced curry.',
                'price' => 190.00,
                'category' => 'Gravy & Curries',
                'image_url' => 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Green Peas Masala',
                'description' => 'Sweet green peas cooked in rich savory tomato cashew gravy.',
                'price' => 190.00,
                'category' => 'Gravy & Curries',
                'image_url' => 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Gobi Manchurian Gravy',
                'description' => 'Crispy cauliflower fried balls tossed in tangy, spicy Indo-Chinese gravy.',
                'price' => 210.00,
                'category' => 'Gravy & Curries',
                'image_url' => 'https://images.unsplash.com/photo-1626777552726-4a6b54c97e46?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Paneer Tikka Masala',
                'description' => 'Grilled tandoori paneer tikka pieces cooked in rich spicy masala gravy.',
                'price' => 260.00,
                'category' => 'Gravy & Curries',
                'image_url' => 'https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Chilli Paneer Gravy',
                'description' => 'Cottage cheese cubes tossed with diced bell peppers and spicy chili soy sauce gravy.',
                'price' => 220.00,
                'category' => 'Gravy & Curries',
                'image_url' => 'https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Veg Kolhapuri',
                'description' => 'Fiery and spicy mixed vegetable curry flavored with Kolhapuri spices.',
                'price' => 230.00,
                'category' => 'Gravy & Curries',
                'image_url' => 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],

            // ------------------- 7. STARTERS -------------------
            [
                'name' => 'Gobi 65',
                'description' => 'Crispy deep-fried cauliflower florets marinated in South Indian red chili spices.',
                'price' => 210.00,
                'category' => 'Starters',
                'image_url' => 'https://images.unsplash.com/photo-1626777552726-4a6b54c97e46?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Mushroom 65',
                'description' => 'Crispy fried button mushrooms coated with spicy batter and curry leaves.',
                'price' => 220.00,
                'category' => 'Starters',
                'image_url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Paneer 65',
                'description' => 'Tender cottage cheese cubes fried with spicy red chili marinade.',
                'price' => 220.00,
                'category' => 'Starters',
                'image_url' => 'https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'French Fries',
                'description' => 'Golden salted potato finger fries served crisp with tomato ketchup.',
                'price' => 130.00,
                'category' => 'Starters',
                'image_url' => 'https://images.unsplash.com/photo-1576107232684-1279f3908594?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Gobi Manchurian Dry',
                'description' => 'Crispy cauliflower bites tossed dry with garlic, soy, and chili sauce.',
                'price' => 210.00,
                'category' => 'Starters',
                'image_url' => 'https://images.unsplash.com/photo-1626777552726-4a6b54c97e46?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Mushroom Manchurian Dry',
                'description' => 'Fried mushrooms tossed with spring onions, garlic, and Chinese sauces.',
                'price' => 220.00,
                'category' => 'Starters',
                'image_url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Paneer Tikka',
                'description' => 'Marinated cottage cheese cubes skewered and grilled in clay tandoor.',
                'price' => 260.00,
                'category' => 'Starters',
                'image_url' => 'https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Mushroom Tikka',
                'description' => 'Spiced button mushrooms roasted over open coals with tandoori spices.',
                'price' => 260.00,
                'category' => 'Starters',
                'image_url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Mushroom Pepper Fry',
                'description' => 'Mushrooms sautéed with crushed black pepper, shallots, and curry leaves.',
                'price' => 220.00,
                'category' => 'Starters',
                'image_url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Mushroom Salt & Pepper',
                'description' => 'Crispy wok-tossed mushrooms seasoned with sea salt and crushed white pepper.',
                'price' => 220.00,
                'category' => 'Starters',
                'image_url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Golden Fry Baby Corn',
                'description' => 'Crispy battered baby corn fried to a golden crunch.',
                'price' => 220.00,
                'category' => 'Starters',
                'image_url' => 'https://images.unsplash.com/photo-1599487488170-d11ec9c172f0?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Baby Corn Salt & Pepper',
                'description' => 'Wok-tossed baby corn seasoned with salt, pepper, and garlic.',
                'price' => 220.00,
                'category' => 'Starters',
                'image_url' => 'https://images.unsplash.com/photo-1599487488170-d11ec9c172f0?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Veg - 99',
                'description' => 'Chef special crispy fried mixed vegetable starter coated in spicy sweet sauce.',
                'price' => 210.00,
                'category' => 'Starters',
                'image_url' => 'https://images.unsplash.com/photo-1599487488170-d11ec9c172f0?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Mushroom - Duplex',
                'description' => 'Stuffed mushroom caps double fried for an extra crunchy texture.',
                'price' => 220.00,
                'category' => 'Starters',
                'image_url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],

            // ------------------- 8. RICE & PULAV -------------------
            [
                'name' => 'Veg Fried Rice',
                'description' => 'Aromatic basmati rice wok-fried with finely diced vegetables and Chinese spices.',
                'price' => 180.00,
                'category' => 'Rice & Pulav',
                'image_url' => 'https://images.unsplash.com/photo-1596560548464-f010549b84d7?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Mushroom Fried Rice',
                'description' => 'Basmati fried rice loaded with sautéed button mushrooms and spring onions.',
                'price' => 210.00,
                'category' => 'Rice & Pulav',
                'image_url' => 'https://images.unsplash.com/photo-1596560548464-f010549b84d7?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Veg Pulav',
                'description' => 'Mildly spiced basmati rice pulav cooked with green peas, carrots, and whole spices.',
                'price' => 200.00,
                'category' => 'Rice & Pulav',
                'image_url' => 'https://images.unsplash.com/photo-1596560548464-f010549b84d7?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Cashewnut Pulav',
                'description' => 'Fragrant rice pulav cooked in ghee and generously studded with roasted golden cashews.',
                'price' => 210.00,
                'category' => 'Rice & Pulav',
                'image_url' => 'https://images.unsplash.com/photo-1596560548464-f010549b84d7?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Peas Pulav',
                'description' => 'Basmati rice cooked with tender sweet green peas and aromatic cumin seeds.',
                'price' => 210.00,
                'category' => 'Rice & Pulav',
                'image_url' => 'https://images.unsplash.com/photo-1596560548464-f010549b84d7?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Paneer Pulav',
                'description' => 'Aromatic basmati rice cooked with golden fried cottage cheese cubes.',
                'price' => 210.00,
                'category' => 'Rice & Pulav',
                'image_url' => 'https://images.unsplash.com/photo-1596560548464-f010549b84d7?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Kashmir Pulav',
                'description' => 'Rich sweet-savory rice pulav garnished with dry fruits, saffron, and fresh fruit pieces.',
                'price' => 210.00,
                'category' => 'Rice & Pulav',
                'image_url' => 'https://images.unsplash.com/photo-1596560548464-f010549b84d7?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Ghee Rice',
                'description' => 'Rich basmati rice cooked in pure cow ghee and garnished with fried onions and cashews.',
                'price' => 180.00,
                'category' => 'Rice & Pulav',
                'image_url' => 'https://images.unsplash.com/photo-1596560548464-f010549b84d7?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],

            // ------------------- 9. ROTI & BREADS -------------------
            [
                'name' => 'Chapathi (2 Pcs)',
                'description' => 'Soft whole wheat handmade flatbreads served with vegetable curry.',
                'price' => 75.00,
                'category' => 'Roti & Breads',
                'image_url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Parota (2 Pcs)',
                'description' => 'Flaky multi-layered South Indian flatbread served with spicy kuruma salna.',
                'price' => 75.00,
                'category' => 'Roti & Breads',
                'image_url' => 'https://images.unsplash.com/photo-1588166524941-3bf61a9c41db?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Roti',
                'description' => 'Whole wheat tandoori flatbread baked crisp in a clay oven.',
                'price' => 30.00,
                'category' => 'Roti & Breads',
                'image_url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Tandoori Paratha',
                'description' => 'Layered whole wheat paratha baked inside clay tandoor and brushed with butter.',
                'price' => 65.00,
                'category' => 'Roti & Breads',
                'image_url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Butter Roti',
                'description' => 'Tandoori whole wheat roti brushed with fresh melted butter.',
                'price' => 55.00,
                'category' => 'Roti & Breads',
                'image_url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Kashmir Naan',
                'description' => 'Soft tandoori naan stuffed with sweet dry fruit paste, cashews, and raisins.',
                'price' => 90.00,
                'category' => 'Roti & Breads',
                'image_url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Butter Naan',
                'description' => 'Soft fluffy tandoori naan bread glazed with rich butter.',
                'price' => 45.00,
                'category' => 'Roti & Breads',
                'image_url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Garlic Naan',
                'description' => 'Soft tandoori naan infused with minced garlic and coriander herbs.',
                'price' => 55.00,
                'category' => 'Roti & Breads',
                'image_url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Kulcha',
                'description' => 'Soft leavened tandoori flatbread sprinkled with onion seeds and herbs.',
                'price' => 60.00,
                'category' => 'Roti & Breads',
                'image_url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Butter Kulcha',
                'description' => 'Soft tandoori kulcha topped with melted butter.',
                'price' => 65.00,
                'category' => 'Roti & Breads',
                'image_url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Aloo Paratha',
                'description' => 'Stuffed flatbread loaded with spiced mashed potato filling served with butter.',
                'price' => 85.00,
                'category' => 'Roti & Breads',
                'image_url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],

            // ------------------- 10. MEALS & VARIETY RICE -------------------
            [
                'name' => 'South Indian Meals',
                'description' => 'Grand thali with Rice, Sambar, Rasam, Kara Kuzhambu, Kootu, Poriyal, Appalam, Sweet & Curd.',
                'price' => 120.00,
                'category' => 'Meals & Variety Rice',
                'image_url' => 'https://images.unsplash.com/photo-1610192244261-3f33de3f55e4?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Mini Meals',
                'description' => 'Compact meals served with Sambar Rice, Curd Rice, Special Variety Rice, Poriyal, and Appalam.',
                'price' => 110.00,
                'category' => 'Meals & Variety Rice',
                'image_url' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'North Indian Meals',
                'description' => 'North Indian platter with Roti, Paneer Gravy, Dhall, Pulav, Sweet, Salad & Papad.',
                'price' => 180.00,
                'category' => 'Meals & Variety Rice',
                'image_url' => 'https://images.unsplash.com/photo-1610192244261-3f33de3f55e4?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Sambar Rice',
                'description' => 'Aromatic lentil sambar rice cooked with garden vegetables and ghee.',
                'price' => 70.00,
                'category' => 'Meals & Variety Rice',
                'image_url' => 'https://images.unsplash.com/photo-1610192244261-3f33de3f55e4?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Curd Rice',
                'description' => 'Soothing chilled creamy curd rice tempered with mustard, curry leaves, and pomegranate.',
                'price' => 70.00,
                'category' => 'Meals & Variety Rice',
                'image_url' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Lemon / Tamarind / Tomato Rice',
                'description' => 'Flavorful South Indian spiced rice prepared with fresh lemon, tangy tamarind, or ripe tomatoes.',
                'price' => 70.00,
                'category' => 'Meals & Variety Rice',
                'image_url' => 'https://images.unsplash.com/photo-1596560548464-f010549b84d7?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],

            // ------------------- 11. BIRYANI -------------------
            [
                'name' => 'Veg Biriyani',
                'description' => 'Fragrant Basmati rice dum cooked with vegetables, mint, ghee, and exotic biryani spices.',
                'price' => 160.00,
                'category' => 'Biryani',
                'image_url' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Mushroom Biriyani',
                'description' => 'Aromatic spiced dum biryani loaded with juicy button mushrooms and fried onions.',
                'price' => 180.00,
                'category' => 'Biryani',
                'image_url' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Paneer Biriyani',
                'description' => 'Flavorful basmati rice dum biryani cooked with marinated paneer cubes and saffron.',
                'price' => 180.00,
                'category' => 'Biryani',
                'image_url' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],

            // ------------------- 12. EVENING & DAY SPECIALS -------------------
            [
                'name' => 'Appam with Coconut Milk or Paya',
                'description' => 'Soft bowl-shaped lace pancake served with sweet coconut milk or spicy veg paya gravy.',
                'price' => 80.00,
                'category' => 'Evening & Day Specials',
                'image_url' => 'https://images.unsplash.com/photo-1610192244261-3f33de3f55e4?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Kuzhi Paniyaram (Sweet / Spicy)',
                'description' => 'Crispy outside, soft inside fermented rice balls prepared sweet or savory with onions.',
                'price' => 80.00,
                'category' => 'Evening & Day Specials',
                'image_url' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Mint Dosa with Vadacurry',
                'description' => 'Fresh mint infused crispy dosa served with spicy Tamil Nadu vadacurry.',
                'price' => 80.00,
                'category' => 'Evening & Day Specials',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Channa Patura',
                'description' => 'Large fluffy deep-fried bread served with authentic spicy chickpea chole masala.',
                'price' => 110.00,
                'category' => 'Evening & Day Specials',
                'image_url' => 'https://images.unsplash.com/photo-1626777552726-4a6b54c97e46?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Ragi Dosa',
                'description' => 'Healthy fingerprint millet crepe crisp roasted with ghee.',
                'price' => 110.00,
                'category' => 'Evening & Day Specials',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Wheat Dosa',
                'description' => 'Nutritious whole wheat crepe crisp roasted and served with chutney and sambar.',
                'price' => 110.00,
                'category' => 'Evening & Day Specials',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Nava Dhaniya Dosa',
                'description' => 'Multi-grain 9-lentil health dosa served with fresh coconut chutney.',
                'price' => 110.00,
                'category' => 'Evening & Day Specials',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Adai Aviyal',
                'description' => 'Protein-rich thick spiced multi-lentil pancake served with traditional coconut aviyal.',
                'price' => 110.00,
                'category' => 'Evening & Day Specials',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ],
            [
                'name' => 'Vendhaya Dosa',
                'description' => 'Soft sponge dosa seasoned with aromatic fenugreek seeds (methi) and cumin.',
                'price' => 110.00,
                'category' => 'Evening & Day Specials',
                'image_url' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80',
                'is_available' => 1
            ]
        ];
    }
}
