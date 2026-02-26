-- ============================================================================
-- SQL to import product prices from xlsx files
-- Generated from: real_data/*.xlsx
-- ============================================================================

SET @branch_id = '019c850c-7294-72ea-9ec5-1b00d6b0bdd8';
SET @updated_at = NOW();


-- ============================================================================
-- HOT KITCHEN Products (product_type_id=1, sales_department_id=1)
-- Source: UNIT PRICE - HOT KITCHEN PD - SEALED.xlsx
-- ============================================================================

UPDATE products SET price = 20000.0, updated_at = @updated_at WHERE name = 'BREAKFAST FOR CHAMPS' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 8000.0, updated_at = @updated_at WHERE name = 'CHICKEN SAUSAGE' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 9000.0, updated_at = @updated_at WHERE name = 'CHICKEN WRAP' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 7500.0, updated_at = @updated_at WHERE name = 'CLASSIC PANCAKE' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 8500.0, updated_at = @updated_at WHERE name = 'CLASSIC SPRINGROLLS' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 8000.0, updated_at = @updated_at WHERE name = 'COLESLAW' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 14000.0, updated_at = @updated_at WHERE name = 'CORNERSTORE''S CHICKEN FETTUCCINE ALFREDO' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 15000.0, updated_at = @updated_at WHERE name = 'CORNERSTORE''S CLUB SANDWICH' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 15000.0, updated_at = @updated_at WHERE name = 'CORNERSTORE''S JUICY BURGER' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 21000.0, updated_at = @updated_at WHERE name = 'CORNERSTORE''S SEAFOOD FETTUCCINE ALFREDO' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 12000.0, updated_at = @updated_at WHERE name = 'CORNERSTORES PASTA BOLOGNESE' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 16000.0, updated_at = @updated_at WHERE name = 'CREAMY PENNE PASTA WITH CHICKEN' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 12500.0, updated_at = @updated_at WHERE name = 'DO IT FOR THE BRITS (SCRAMBLED)' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 12500.0, updated_at = @updated_at WHERE name = 'DO IT FOR THE BRITS (OMELETTE)' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 17000.0, updated_at = @updated_at WHERE name = 'DOUBLE AGENT BURGER' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 8000.0, updated_at = @updated_at WHERE name = 'FRENCH FRIES (SIDE ATTRACTION)' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 14500.0, updated_at = @updated_at WHERE name = 'FRENCH TOAST WITH EGGS & BACON (OMELETTE)' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 15000.0, updated_at = @updated_at WHERE name = 'GRILLED CHICKEN CAESAR SALAD' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 13000.0, updated_at = @updated_at WHERE name = 'JAMBALAYA RICE' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 17000.0, updated_at = @updated_at WHERE name = 'LONDON STYLED BATTERED FISH & CHIPS' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 13000.0, updated_at = @updated_at WHERE name = 'MAC AND CHEESE' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 6500.0, updated_at = @updated_at WHERE name = 'OMELETTE' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 8500.0, updated_at = @updated_at WHERE name = 'SAMOSA' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 9000.0, updated_at = @updated_at WHERE name = 'SPICY BEEF WRAP' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 15500.0, updated_at = @updated_at WHERE name = 'SPICY QUARTER GRILLED CHICKEN' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 15500.0, updated_at = @updated_at WHERE name = 'SPICY WINGS' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 17500.0, updated_at = @updated_at WHERE name = 'SPICY WINGS WITH FRENCH FRIES' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 6500.0, updated_at = @updated_at WHERE name = 'SUNNY SIDE UP' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 12500.0, updated_at = @updated_at WHERE name = 'WAFFLES WITH WHIPPED CREAM & CHOCOLATE SAUCE' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 12500.0, updated_at = @updated_at WHERE name = 'YANKEE BREAKFAST (OMELETTE)' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 12500.0, updated_at = @updated_at WHERE name = 'YANKEE BREAKFAST (SCRAMBLED)' AND product_type_id = 1 AND branch_id = @branch_id;
UPDATE products SET price = 12500.0, updated_at = @updated_at WHERE name = 'YANKEE BREAKFAST (SUNNYSIDE)' AND product_type_id = 1 AND branch_id = @branch_id;

-- ============================================================================
-- PASTRY Products (product_type_id=2, sales_department_id=2)
-- Source: UNIT PRICE - PASTRY PD - SEALED.xlsx
-- ============================================================================

UPDATE products SET price = 24000.0, updated_at = @updated_at WHERE name = '6 INCHES CHOCOLATE BENTO' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3500.0, updated_at = @updated_at WHERE name = '6 INCHES VANILLA  CHOCOLATE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3000.0, updated_at = @updated_at WHERE name = '6 INCH VANILLA BENTO' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 20000.0, updated_at = @updated_at WHERE name = '8 INCHES VANILA DRIED CAKE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 2200.0, updated_at = @updated_at WHERE name = 'ALMOND CROISSANT' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 2500.0, updated_at = @updated_at WHERE name = 'BAGUETTE BREAD' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3000.0, updated_at = @updated_at WHERE name = 'BEEF FRIAND' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 10000.0, updated_at = @updated_at WHERE name = 'BIG BANANA BREAD' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 7000.0, updated_at = @updated_at WHERE name = 'BIG CHIN CHIN' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3500.0, updated_at = @updated_at WHERE name = 'BLACK FOREST SLICE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 2200.0, updated_at = @updated_at WHERE name = 'BLACKFOREST CUP CAKE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 7000.0, updated_at = @updated_at WHERE name = 'BROWNIES CHEESE CAKE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 7000.0, updated_at = @updated_at WHERE name = 'CARAMEL CHEESECAKE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 2500.0, updated_at = @updated_at WHERE name = 'CARAMEL CROISSANT' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3000.0, updated_at = @updated_at WHERE name = 'CARAMEL CUPCAKE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 2200.0, updated_at = @updated_at WHERE name = 'CHEESE CROISSANT' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3000.0, updated_at = @updated_at WHERE name = 'CHICKEN FRIAND' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 24000.0, updated_at = @updated_at WHERE name = 'CHOCOLATE  CHIPS' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 2200.0, updated_at = @updated_at WHERE name = 'CHOCOLATE  MOUSE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3000.0, updated_at = @updated_at WHERE name = 'CHOCOLATE CHIPS COOKIES' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 1800.0, updated_at = @updated_at WHERE name = 'CHOCOLATE GLAZED DOUGHNUT' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3500.0, updated_at = @updated_at WHERE name = 'CHOCOLATE SPONGE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 1500.0, updated_at = @updated_at WHERE name = 'CINNAMON DOUGHNUT' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 2500.0, updated_at = @updated_at WHERE name = 'CINNAMON ROLL' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 4600.0, updated_at = @updated_at WHERE name = 'COCONUT BANANA BREAD' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 1500.0, updated_at = @updated_at WHERE name = 'COCONUT DOUGHNUT' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 2200.0, updated_at = @updated_at WHERE name = 'CREAM FILLING' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 1500.0, updated_at = @updated_at WHERE name = 'CRISPY DOUGHNUT' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 4000.0, updated_at = @updated_at WHERE name = 'DOUBLE CHOCOLATE CHIPS COOKIES' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 7000.0, updated_at = @updated_at WHERE name = 'FULL CAKE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3500.0, updated_at = @updated_at WHERE name = 'GINGER BREAD HOUSE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 1500.0, updated_at = @updated_at WHERE name = 'HAZELNUT DOUGHNUT' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 2000.0, updated_at = @updated_at WHERE name = 'ICING COOKIES' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 1500.0, updated_at = @updated_at WHERE name = 'JAM DOUGHNUT' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 2500.0, updated_at = @updated_at WHERE name = 'MARCARONS' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3000.0, updated_at = @updated_at WHERE name = 'MILLIAN' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3000.0, updated_at = @updated_at WHERE name = 'MINI CROISSANTS' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3500.0, updated_at = @updated_at WHERE name = 'MOCHA CAKE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 6000.0, updated_at = @updated_at WHERE name = 'NUTELLA BANANA BREAD' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 2500.0, updated_at = @updated_at WHERE name = 'NUTELLA CROISSANTS' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 7000.0, updated_at = @updated_at WHERE name = 'OREOS CUP CAKE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 1500.0, updated_at = @updated_at WHERE name = 'OREOS DOUGHNUT' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 7000.0, updated_at = @updated_at WHERE name = 'PELITE GATEAU(STROUS MOUSE)' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 2500.0, updated_at = @updated_at WHERE name = 'PIAN AU CHOCOLATE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 2200.0, updated_at = @updated_at WHERE name = 'PLAIN CROISSANT' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 1500.0, updated_at = @updated_at WHERE name = 'PLAIN DOUGHNUT' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 1500.0, updated_at = @updated_at WHERE name = 'PLAIN POPCORN' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 7000.0, updated_at = @updated_at WHERE name = 'RASPBERRY CHEESE CAKE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3000.0, updated_at = @updated_at WHERE name = 'RED VELVET CUP CAKE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 5500.0, updated_at = @updated_at WHERE name = 'REDVELVET MARBLE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 5500.0, updated_at = @updated_at WHERE name = 'REDVELVET SLICE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3000.0, updated_at = @updated_at WHERE name = 'SAUSAGE CROISSANT' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 4600.0, updated_at = @updated_at WHERE name = 'SMALL BANANA BREAD' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 4000.0, updated_at = @updated_at WHERE name = 'SMALL CHIN CHIN' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 1500.0, updated_at = @updated_at WHERE name = 'SPECIAL DOUGHNUT' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 5500.0, updated_at = @updated_at WHERE name = 'STRAWBERRY CHEESE CAKE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 5500.0, updated_at = @updated_at WHERE name = 'TRES LECHES' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 5500.0, updated_at = @updated_at WHERE name = 'TRIO CLASSIC' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3500.0, updated_at = @updated_at WHERE name = 'VANILLA CUPCAKE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3500.0, updated_at = @updated_at WHERE name = 'VANILLA SPONGE' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3500.0, updated_at = @updated_at WHERE name = 'WHITE FOREST' AND product_type_id = 2 AND branch_id = @branch_id;
UPDATE products SET price = 3000.0, updated_at = @updated_at WHERE name = 'YURGI COOKIES' AND product_type_id = 2 AND branch_id = @branch_id;

-- ============================================================================
-- GELATO Products (product_type_id=3, sales_department_id=3)
-- Source: UNIT PRICE - GELATO PD - SEALED.xlsx
-- ============================================================================

UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'AFTER EIGHT FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'AMARENA FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'APPLE FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'BISCOTTO FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'BLACK DIAMOND FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'BLUE SKY FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'BROWNIES FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'BUBBLE GUM FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'CARAMEL FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'CHOCO COCONUT FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'COOKIES LEMON FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'COOKKIES AND CREAM FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'CREAM BRULEE FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'FUNKY MALTY FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'GINGER SNAP FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'GREEN APPLE FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'MALAGA FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'MIDNIGHT CHOCOLATE FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'PEACH FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'PEANUT BUTTER FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'PISTACHIO FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'RUBY CHEESE CAKE FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'SALTED BUTTER CARAMEL FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'STRACCIATELLA FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'STRAW BERRY FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'VANILLA FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;
UPDATE products SET price = 100.0, updated_at = @updated_at WHERE name = 'WHISKY FLAVOR' AND product_type_id = 3 AND branch_id = @branch_id;

-- ============================================================================
-- CORNER STORE Products (product_type_id=4, sales_department_id=6)
-- Source: UNIT PRICE - CONRNER STORE PD.xlsx
-- ============================================================================

UPDATE products SET price = 7000.0, updated_at = @updated_at WHERE name = 'AFFOGATO COFFEE' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 5500.0, updated_at = @updated_at WHERE name = 'BAILEYS ESPRESSO ON THE ROCK COFFEE' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 4500.0, updated_at = @updated_at WHERE name = 'CAPPUCCINO COFFEE' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 5500.0, updated_at = @updated_at WHERE name = 'CARAMEL HOT CHOCOLATE' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 6000.0, updated_at = @updated_at WHERE name = 'CARAMEL LATTE' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 4500.0, updated_at = @updated_at WHERE name = 'DOUBLE ESPRESSO COFFEE' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 5500.0, updated_at = @updated_at WHERE name = 'ESPRESSO MARTINI COFFEE' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 6000.0, updated_at = @updated_at WHERE name = 'FRAPUCCINO COFFEE (CARAMEL)' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 6000.0, updated_at = @updated_at WHERE name = 'FRAPUCCINO COFFEE (CHOCOLATE)' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 5500.0, updated_at = @updated_at WHERE name = 'FROTHY TOP CHOCOLATE' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 5500.0, updated_at = @updated_at WHERE name = 'GIN N TONIC' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 5000.0, updated_at = @updated_at WHERE name = 'HOUSE SPECIAL ORGANIC BREW' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 7500.0, updated_at = @updated_at WHERE name = 'JAMAICAN RUM PUNCH' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 4500.0, updated_at = @updated_at WHERE name = 'LATTE COFFE' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 4500.0, updated_at = @updated_at WHERE name = 'LATTE COFFEE' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 4000.0, updated_at = @updated_at WHERE name = 'LEMON ICED TEA' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 4000.0, updated_at = @updated_at WHERE name = 'LONG BLACK OR AMERICANO COFFEE' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 8500.0, updated_at = @updated_at WHERE name = 'LONG ISLAND' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 8000.0, updated_at = @updated_at WHERE name = 'MARGARITA' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 5000.0, updated_at = @updated_at WHERE name = 'MOCHA COFFEE' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 4000.0, updated_at = @updated_at WHERE name = 'PEACH ICED TEA' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 6000.0, updated_at = @updated_at WHERE name = 'SPANISH LATTE' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 6000.0, updated_at = @updated_at WHERE name = 'STRAWBERRY MILKSHAKE' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 7000.0, updated_at = @updated_at WHERE name = 'TEQUILA SUNRISE' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 5500.0, updated_at = @updated_at WHERE name = 'VANILLA MILKSHAKE' AND product_type_id = 4 AND branch_id = @branch_id;
UPDATE products SET price = 4500.0, updated_at = @updated_at WHERE name = 'VODKA SHOT' AND product_type_id = 4 AND branch_id = @branch_id;

