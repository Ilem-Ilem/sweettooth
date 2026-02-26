-- ============================================================================
-- SWEETTOOTH PRODUCTS IMPORT
-- Generated from: XLSX price files + JSON production data
-- ============================================================================

SET @branch_id = '019c850c-7294-72ea-9ec5-1b00d6b0bdd8';
SET @now = NOW();

-- ============================================================================
-- STEP 1: Delete existing products 
-- ============================================================================
-- Run: TRUNCATE TABLE products; 

-- ============================================================================
-- STEP 2: Insert new products
-- ============================================================================

INSERT INTO products (id, name, sku, branch_id, product_type_id, sales_department_id, price, cost, is_active, is_available, created_at, updated_at) VALUES
('81d65978-be35-4480-be12-85b5108134b6', 'BREAKFAST FOR CHAMPS', '60842', @branch_id, 1, 7, 20000.0, 899352.0, 1, 1, @now, @now),
('286ef983-3645-474c-8693-760d1f776253', 'CHICKEN SAUSAGE', '37006', @branch_id, 1, 7, 8000.0, 32.0, 1, 1, @now, @now),
('48ebc718-d766-4b19-9a64-12e476f449c0', 'CHICKEN WRAP', '37324', @branch_id, 1, 7, 9000.0, 33088.2, 1, 1, @now, @now),
('c56b3cfc-c422-4494-a7f0-6e7b2b6c0a32', 'CLASSIC PANCAKE', '50838', @branch_id, 1, 7, 7500.0, 71000.0, 1, 1, @now, @now),
('5bde0038-3654-431f-b96a-16f6e16fc4c6', 'CLASSIC SPRINGROLLS', '11742', @branch_id, 1, 7, 8500.0, 74064.0, 1, 1, @now, @now),
('649d7fb5-46f2-4979-aa47-35df5647530a', 'COLESLAW', '40893', @branch_id, 1, 7, 8000.0, 0, 1, 1, @now, @now),
('1a694ddc-0460-4b3d-958a-488756b18ba8', 'CORNERSTORE'S CHICKEN FETTUCCINE ALFREDO', '91614', @branch_id, 1, 7, 14000.0, 450976.5, 1, 1, @now, @now),
('11d1139d-1e02-49c1-ac7a-00f72f2ae95f', 'CORNERSTORE'S CLUB SANDWICH', '79411', @branch_id, 1, 7, 15000.0, 2489982.0, 1, 1, @now, @now),
('76247e7d-64fe-4c45-bf21-ccb9c06f9105', 'CORNERSTORE'S JUICY BURGER', '82435', @branch_id, 1, 7, 15000.0, 5231296.0, 1, 1, @now, @now),
('6abddd20-4a0b-4d8a-8a05-6aa27647e9b5', 'CORNERSTORE'S SEAFOOD FETTUCCINE ALFREDO', '35116', @branch_id, 1, 7, 21000.0, 555652.5, 1, 1, @now, @now),
('6fc8ecce-0f36-4416-986c-21eb86a136bf', 'CORNERSTORES PASTA BOLOGNESE', '10534', @branch_id, 1, 7, 12000.0, 308892.0, 1, 1, @now, @now),
('393726a7-a058-4df3-a999-27be2ca86031', 'CREAMY PENNE PASTA WITH CHICKEN', '21952', @branch_id, 1, 7, 16000.0, 859381.2, 1, 1, @now, @now),
('c480b616-5b6f-4c01-a2d5-5582c7e7874c', 'DO IT FOR THE BRITS (SCRAMBLED)', '75374', @branch_id, 1, 7, 12500.0, 214287.0, 1, 1, @now, @now),
('c5a0472e-189a-4c3b-bdc5-946e3685690f', 'DO IT FOR THE BRITS (OMELETTE)', '26001', @branch_id, 1, 7, 12500.0, 0, 1, 1, @now, @now),
('9054337f-c65e-410b-92d2-5074baa8965d', 'DOUBLE AGENT BURGER', '95195', @branch_id, 1, 7, 17000.0, 2204948.0, 1, 1, @now, @now),
('e7a32ae0-66c4-4456-9e77-55355381a57b', 'FRENCH FRIES (SIDE ATTRACTION)', '29174', @branch_id, 1, 7, 8000.0, 950128.0, 1, 1, @now, @now),
('af8fab95-897d-47a9-8b57-b33a5c034e45', 'FRENCH TOAST WITH EGGS & BACON (OMELETTE)', '77659', @branch_id, 1, 7, 14500.0, 71215.0, 1, 1, @now, @now),
('4a9d598b-529b-40e9-a97b-b25902144ffa', 'GRILLED CHICKEN CAESAR SALAD', '11913', @branch_id, 1, 7, 15000.0, 1302332.0, 1, 1, @now, @now),
('1a7df63b-43ee-48eb-b347-ab52df31a0df', 'JAMBALAYA RICE', '54651', @branch_id, 1, 7, 13000.0, 3691097.0, 1, 1, @now, @now),
('5a5f05f5-1f2f-418c-9a95-cdb6ae407d28', 'LONDON STYLED BATTERED FISH & CHIPS', '39657', @branch_id, 1, 7, 17000.0, 1024307.5, 1, 1, @now, @now),
('bec40833-fc66-4ce3-80f0-2a0d18649fc9', 'MAC AND CHEESE', '53483', @branch_id, 1, 7, 13000.0, 2830700.5, 1, 1, @now, @now),
('4eb1bac3-8265-463b-be84-d600697cae7b', 'OMELETTE', '54559', @branch_id, 1, 7, 6500.0, 0.0, 1, 1, @now, @now),
('17bb17ae-23e9-4d09-9c61-c76b5eb9a47a', 'SAMOSA', '39609', @branch_id, 1, 7, 8500.0, 74064.0, 1, 1, @now, @now),
('0dc44ba5-46fb-48ad-bb1b-cbfe57744297', 'SPICY BEEF WRAP', '60708', @branch_id, 1, 7, 9000.0, 239544.0, 1, 1, @now, @now),
('96c63b41-ccfd-4cef-8b68-ea6be4f30ecf', 'SPICY QUARTER GRILLED CHICKEN', '11594', @branch_id, 1, 7, 15500.0, 70064.0, 1, 1, @now, @now),
('c81dfc13-bc15-4476-9e41-62274a0c3ee5', 'SPICY WINGS', '10345', @branch_id, 1, 7, 15500.0, 71064.0, 1, 1, @now, @now),
('9023a734-1f43-4dce-a44a-d1d8c08c0073', 'SPICY WINGS WITH FRENCH FRIES', '96501', @branch_id, 1, 7, 17500.0, 38750128.0, 1, 1, @now, @now),
('48149c2a-3b33-44cf-b665-95483d6c053b', 'SUNNY SIDE UP', '49671', @branch_id, 1, 7, 6500.0, 5643.5, 1, 1, @now, @now),
('cde2d005-1215-41a0-9ca0-9d84fbccadce', 'WAFFLES WITH WHIPPED CREAM & CHOCOLATE SAUCE', '38577', @branch_id, 1, 7, 12500.0, 345000.0, 1, 1, @now, @now),
('b15a01ea-b968-45b4-85d3-b8cf28f3abbf', 'YANKEE BREAKFAST (OMELETTE)', '77016', @branch_id, 1, 7, 12500.0, 561064.0, 1, 1, @now, @now),
('7457d7c0-6cd4-4ad0-8f2e-61e30462e90c', 'YANKEE BREAKFAST (SCRAMBLED)', '39594', @branch_id, 1, 7, 12500.0, 561914.0, 1, 1, @now, @now),
('8173d7e5-bc75-43ab-8523-8793e9b5aed1', 'YANKEE BREAKFAST (SUNNYSIDE)', '11473', @branch_id, 1, 7, 12500.0, 281957.0, 1, 1, @now, @now),
('fd96e080-e417-4125-a3f4-102d8ad72603', '6 INCHES CHOCOLATE BENTO', '56115', @branch_id, 2, 5, 24000.0, 0, 1, 1, @now, @now),
('0611e1ca-6768-4fde-8029-5bcc5d9048cd', '6 INCHES VANILLA  CHOCOLATE', '64998', @branch_id, 2, 5, 3500.0, 0, 1, 1, @now, @now),
('f4ac479d-e2ee-4b49-9787-791108606f6d', '6 INCH VANILLA BENTO', '15023', @branch_id, 2, 5, 3000.0, 0, 1, 1, @now, @now),
('2686ad0a-58ae-4b39-8f5e-9fe7fe55b3ec', '8 INCHES VANILA DRIED CAKE', '10597', @branch_id, 2, 6, 20000.0, 0, 1, 1, @now, @now),
('4f0c45d1-e9fb-494f-8dfa-a87b4d9ca0cd', 'ALMOND CROISSANT', '79555', @branch_id, 2, 5, 2200.0, 0.0, 1, 1, @now, @now),
('e504aa21-7c7f-4e73-a78b-3bf6a5a78357', 'BAGUETTE BREAD', '71029', @branch_id, 2, 5, 2500.0, 78887.5, 1, 1, @now, @now),
('d3763dfb-6177-45af-ac18-10e7c3f7dbd7', 'BEEF FRIAND', '91482', @branch_id, 2, 5, 3000.0, 5212.5, 1, 1, @now, @now),
('8d9b7a63-c49c-4019-9d88-620902683f3a', 'BIG BANANA BREAD', '47193', @branch_id, 2, 5, 10000.0, 2182.06, 1, 1, @now, @now),
('0aa5ba8f-bb44-40c7-957a-571b0861fb0a', 'BIG CHIN CHIN', '20070', @branch_id, 2, 6, 7000.0, 0, 1, 1, @now, @now),
('15565372-b893-47f8-aba6-315b1fd44154', 'BLACK FOREST SLICE', '91599', @branch_id, 2, 6, 3500.0, 4326.3, 1, 1, @now, @now),
('410e0ac0-5f46-4af2-a86d-7d99c6011bd4', 'BLACKFOREST CUP CAKE', '16764', @branch_id, 2, 6, 2200.0, 0, 1, 1, @now, @now),
('2edd0246-04e1-4dc5-8a27-c31372cd47d7', 'BROWNIES CHEESE CAKE', '15035', @branch_id, 2, 6, 7000.0, 1681.27, 1, 1, @now, @now),
('c05bb32c-06f4-4818-946b-1e938d0fc7f9', 'CARAMEL CHEESECAKE', '49201', @branch_id, 2, 6, 7000.0, 0, 1, 1, @now, @now),
('14ccc314-824b-4da6-b2e9-898f97bf90a8', 'CARAMEL CROISSANT', '42935', @branch_id, 2, 5, 2500.0, 42612.25, 1, 1, @now, @now),
('00b70ddb-e04b-4e40-aad4-61a0f87d3ef8', 'CARAMEL CUPCAKE', '53324', @branch_id, 2, 6, 3000.0, 2309.2, 1, 1, @now, @now),
('d4181b1b-beb4-4109-affd-46c19d964a33', 'CHEESE CROISSANT', '18121', @branch_id, 2, 5, 2200.0, 2806012.95, 1, 1, @now, @now),
('d86119b9-ae0e-4163-bf9c-c549ca4fa4fd', 'CHICKEN FRIAND', '26437', @branch_id, 2, 5, 3000.0, 5212.5, 1, 1, @now, @now),
('6e07be50-e433-488b-9a5e-c9fcaa7a0da4', 'CHOCOLATE  CHIPS', '20420', @branch_id, 2, 5, 24000.0, 0, 1, 1, @now, @now),
('a9eb612c-6392-4944-8c1f-af427004a6c0', 'CHOCOLATE  MOUSE', '74250', @branch_id, 2, 5, 2200.0, 0, 1, 1, @now, @now),
('ff7efc56-61d1-47e8-9ed2-1f3231cbc267', 'CHOCOLATE CHIPS COOKIES', '95678', @branch_id, 2, 5, 3000.0, 0, 1, 1, @now, @now),
('ffc5c2a0-37ed-4f20-88c9-39714990024f', 'CHOCOLATE GLAZED DOUGHNUT', '14290', @branch_id, 2, 5, 1800.0, 30940.83, 1, 1, @now, @now),
('ff920cc0-e192-473b-bf05-6728518e57da', 'CHOCOLATE SPONGE', '36599', @branch_id, 2, 6, 3500.0, 0, 1, 1, @now, @now),
('a002c592-e4b7-4c97-9149-70fb426270fb', 'CINNAMON DOUGHNUT', '34100', @branch_id, 2, 5, 1500.0, 25763.73, 1, 1, @now, @now),
('c9110023-a9e6-404f-a43a-a8694bcc1f73', 'CINNAMON ROLL', '44137', @branch_id, 2, 5, 2500.0, 42000.0, 1, 1, @now, @now),
('ba80ac0a-0a55-4ca9-9e1b-13254872c8b3', 'COCONUT BANANA BREAD', '21002', @branch_id, 2, 5, 4600.0, 1550.27, 1, 1, @now, @now),
('49932ba2-7f0f-448a-9135-a76db33bd5ca', 'COCONUT DOUGHNUT', '91079', @branch_id, 2, 5, 1500.0, 17150.24, 1, 1, @now, @now),
('421dc1c0-e843-4932-88ab-c1a59d43d859', 'CREAM FILLING', '39262', @branch_id, 2, 5, 2200.0, 0, 1, 1, @now, @now),
('5edad35e-5a68-45d9-9403-b1fc32ebc756', 'CRISPY DOUGHNUT', '79925', @branch_id, 2, 5, 1500.0, 15352.87, 1, 1, @now, @now),
('8be4f197-c04c-4340-8b8d-64405b8b744a', 'DOUBLE CHOCOLATE CHIPS COOKIES', '97693', @branch_id, 2, 5, 4000.0, 0, 1, 1, @now, @now),
('4bf6ecc0-6414-4fd3-918c-1e08ea1ce5d0', 'FULL CAKE', '98769', @branch_id, 2, 6, 7000.0, 0, 1, 1, @now, @now),
('2476ebab-f71f-401a-a5d4-eeaec0a88888', 'GINGER BREAD HOUSE', '28129', @branch_id, 2, 5, 3500.0, 0, 1, 1, @now, @now),
('a5ebb899-2584-401f-a629-2379c087ad9e', 'HAZELNUT DOUGHNUT', '47615', @branch_id, 2, 5, 1500.0, 25763.73, 1, 1, @now, @now),
('5e0f3fed-2c94-4361-942f-75c2efa823fc', 'ICING COOKIES', '19502', @branch_id, 2, 5, 2000.0, 5105.68, 1, 1, @now, @now),
('942616f5-38b2-42a0-8b65-d92f8fcf49e8', 'JAM DOUGHNUT', '62191', @branch_id, 2, 5, 1500.0, 0, 1, 1, @now, @now),
('51a34ee2-de28-4c57-b564-9d5a03e3d789', 'MARCARONS', '24908', @branch_id, 2, 5, 2500.0, 0, 1, 1, @now, @now),
('f3b747e6-505e-4330-87d6-ad0b6df9ec19', 'MILLIAN', '55736', @branch_id, 2, 5, 3000.0, 0, 1, 1, @now, @now),
('f4dca279-c5a1-4838-a6cb-652dec05b71c', 'MINI CROISSANTS', '53199', @branch_id, 2, 5, 3000.0, 0, 1, 1, @now, @now),
('cf9a2014-51c2-4e51-8324-bfe0ccf4c613', 'MOCHA CAKE', '79920', @branch_id, 2, 6, 3500.0, 6629.4, 1, 1, @now, @now),
('d5b4b300-eb1c-45c7-9f8c-e63b5c108e2e', 'NUTELLA BANANA BREAD', '25012', @branch_id, 2, 5, 6000.0, 1653862.76, 1, 1, @now, @now),
('60113e5f-b46f-4806-9ecc-290fac025e4e', 'NUTELLA CROISSANTS', '94269', @branch_id, 2, 5, 2500.0, 0, 1, 1, @now, @now),
('065b55a9-a784-4fa7-9964-b6359bf4c227', 'OREOS CUP CAKE', '48351', @branch_id, 2, 6, 7000.0, 0, 1, 1, @now, @now),
('13a6f1b4-2499-41f0-b744-13c6631e61ac', 'OREOS DOUGHNUT', '60496', @branch_id, 2, 5, 1500.0, 0, 1, 1, @now, @now),
('cc66c90b-70f7-4de4-ac81-41eabd75d833', 'PELITE GATEAU(STROUS MOUSE)', '76707', @branch_id, 2, 5, 7000.0, 0, 1, 1, @now, @now),
('9f5c461f-9cf6-418f-9e0a-c9745fbdc66e', 'PIAN AU CHOCOLATE', '15901', @branch_id, 2, 5, 2500.0, 0, 1, 1, @now, @now),
('d085f3e7-d75a-4374-a438-08297b313372', 'PLAIN CROISSANT', '19818', @branch_id, 2, 5, 2200.0, 46112.95, 1, 1, @now, @now),
('6f04e40a-0b24-4bb7-adae-c8805c63014d', 'PLAIN DOUGHNUT', '57446', @branch_id, 2, 5, 1500.0, 25763.73, 1, 1, @now, @now),
('c162a809-d6f9-4b62-8257-a019c389b964', 'PLAIN POPCORN', '85408', @branch_id, 2, 5, 1500.0, 7955.0, 1, 1, @now, @now),
('e8026783-0f01-4f47-98ed-b4410bfc7250', 'RASPBERRY CHEESE CAKE', '28540', @branch_id, 2, 6, 7000.0, 827014.92, 1, 1, @now, @now),
('7f861765-e9ad-4aad-a996-3a94df8438b2', 'RED VELVET CUP CAKE', '81619', @branch_id, 2, 6, 3000.0, 0, 1, 1, @now, @now),
('4b6811f5-0c69-4964-8c73-ea869bb65930', 'REDVELVET MARBLE', '48903', @branch_id, 2, 5, 5500.0, 0, 1, 1, @now, @now),
('d123ec2a-f70c-4107-83d3-7b18ca01885a', 'REDVELVET SLICE', '98635', @branch_id, 2, 6, 5500.0, 0, 1, 1, @now, @now),
('26ff1adb-502d-474c-9478-5b705b0fa6bd', 'SAUSAGE CROISSANT', '43131', @branch_id, 2, 5, 3000.0, 46112.95, 1, 1, @now, @now),
('93e20426-6b32-47f8-8651-e28675911b59', 'SMALL BANANA BREAD', '20524', @branch_id, 2, 5, 4600.0, 1550.27, 1, 1, @now, @now),
('36f8b2af-37de-4fe8-a202-70e25724552a', 'SMALL CHIN CHIN', '73428', @branch_id, 2, 5, 4000.0, 0, 1, 1, @now, @now),
('87d73b6c-3c7d-430e-88e9-1c61eff58704', 'SPECIAL DOUGHNUT', '36032', @branch_id, 2, 5, 1500.0, 25769.21, 1, 1, @now, @now),
('39019df7-0ad6-4cea-89bc-faeb73c0879a', 'STRAWBERRY CHEESE CAKE', '56257', @branch_id, 2, 6, 5500.0, 491034.89, 1, 1, @now, @now),
('47d9001f-a791-420a-a215-1e1ae3963c54', 'TRES LECHES', '40445', @branch_id, 2, 5, 5500.0, 100902.68, 1, 1, @now, @now),
('2046b184-174c-4334-94fa-4e119fbb9fe4', 'TRIO CLASSIC', '53280', @branch_id, 2, 5, 5500.0, 0, 1, 1, @now, @now),
('20371473-200e-41b6-904f-10700c308d97', 'VANILLA CUPCAKE', '96446', @branch_id, 2, 6, 3500.0, 28262.0, 1, 1, @now, @now),
('aa24106f-21d2-4fba-8972-5fc95d10a472', 'VANILLA SPONGE', '71558', @branch_id, 2, 5, 3500.0, 0, 1, 1, @now, @now),
('12056a7c-188c-4401-abaf-30f9c95a6c13', 'WHITE FOREST', '44467', @branch_id, 2, 5, 3500.0, 0, 1, 1, @now, @now),
('c1d500ef-2b7b-48d3-a525-686cf73aec5b', 'YURGI COOKIES', '23821', @branch_id, 2, 5, 3000.0, 0, 1, 1, @now, @now),
('d3265b7d-8488-4eb9-83b2-0dec0af59df0', 'AFTER EIGHT FLAVOR', '91830', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('1e5e122f-da10-4496-8d5f-6c8edfb03f14', 'AMARENA FLAVOR', '15418', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('0666dec8-e70a-41c1-a056-7deea7362915', 'APPLE FLAVOR', '35484', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('6e666cd3-871e-49c6-9b25-1260bf18bf62', 'BISCOTTO FLAVOR', '71325', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('f77d373a-b1a3-40b5-9f41-c68bbf9eb736', 'BLACK DIAMOND FLAVOR', '53389', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('7ad28df4-f130-42e0-b05a-091cba7eeb3a', 'BLUE SKY FLAVOR', '48345', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('ed72ea84-a41f-44b1-9473-1356aefd9720', 'BROWNIES FLAVOR', '43924', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('7182aab5-ca15-4b10-95ae-91f85f6f67ae', 'BUBBLE GUM FLAVOR', '19924', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('6315c302-0985-41df-9d81-9e917a674fdd', 'CARAMEL FLAVOR', '44198', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('189ef88f-24d6-4c73-9822-171b64e6dbd9', 'CHOCO COCONUT FLAVOR', '21730', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('9bca1afb-1c68-4c98-810c-c516d88f6c59', 'COOKIES LEMON FLAVOR', '49379', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('f9901c70-4587-4282-bc53-426c389b0eee', 'COOKKIES AND CREAM FLAVOR', '41650', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('06902d8d-a958-4bcd-ad4f-b6279b140949', 'CREAM BRULEE FLAVOR', '15979', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('989b65e4-c896-4ecf-bcb5-bdd5facce51a', 'FUNKY MALTY FLAVOR', '13131', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('becec6a0-142a-4812-abaa-3efe227edb72', 'GINGER SNAP FLAVOR', '10782', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('559bd6bc-9b9f-4614-b688-a641ec743af7', 'GREEN APPLE FLAVOR', '99991', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('ea1945eb-ba1b-4b38-88e1-d13c7710d441', 'MALAGA FLAVOR', '27430', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('522129c0-3261-4dbf-bbcb-6f691c5c3cc4', 'MIDNIGHT CHOCOLATE FLAVOR', '76635', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('aa98b543-f202-4d5a-8f77-9d24a20d4a17', 'PEACH FLAVOR', '64798', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('0019ef1e-36c4-4070-a588-5b9e1188a070', 'PEANUT BUTTER FLAVOR', '28406', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('dc5c5174-a8ff-4a91-a19a-fc579eb76654', 'PISTACHIO FLAVOR', '67994', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('f0dad8b8-e4e6-41f1-bbe1-460124a18b9a', 'RUBY CHEESE CAKE FLAVOR', '25517', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('33223b18-9f60-4c73-b0e9-cafa81876e00', 'SALTED BUTTER CARAMEL FLAVOR', '29987', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('b5395e9f-c327-4450-8f5e-a000f533b9e3', 'STRACCIATELLA FLAVOR', '83207', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('72cf4223-089d-402e-a2f0-ef2fce89ea3d', 'STRAW BERRY FLAVOR', '39885', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('1bc27874-b05a-4c6d-8560-409739598e76', 'VANILLA FLAVOR', '83893', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('e08ac2cd-473e-4713-a9d4-633792e8132e', 'WHISKY FLAVOR', '97847', @branch_id, 3, 5, 100.0, 0, 1, 1, @now, @now),
('e39a9e2d-28fe-4b66-b20e-c25ef96dc8f0', 'AFFOGATO COFFEE', '24869', @branch_id, 4, 7, 7000.0, 0.0, 1, 1, @now, @now),
('84d51438-7eab-4e27-a0c8-90f292c343e6', 'BAILEYS ESPRESSO ON THE ROCK COFFEE', '98282', @branch_id, 4, 7, 5500.0, 1100000.0, 1, 1, @now, @now),
('1d589ffe-2dc3-471b-9fbe-d0b7d2217557', 'CAPPUCCINO COFFEE', '62168', @branch_id, 4, 7, 4500.0, 27540.0, 1, 1, @now, @now),
('c8a99579-8c30-47ca-937f-8dc02538cd6a', 'CARAMEL HOT CHOCOLATE', '58777', @branch_id, 4, 7, 5500.0, 2284.6, 1, 1, @now, @now),
('eef3ef1e-ef51-494b-8298-22abb12bb424', 'CARAMEL LATTE', '81322', @branch_id, 4, 7, 6000.0, 600.0, 1, 1, @now, @now),
('9fd84663-b175-4c85-892c-2668e1b73184', 'DOUBLE ESPRESSO COFFEE', '91961', @branch_id, 4, 7, 4500.0, 0.0, 1, 1, @now, @now),
('cec3f0ae-6b9e-4e8c-a4b7-5b2dd4db0b1d', 'ESPRESSO MARTINI COFFEE', '68292', @branch_id, 4, 7, 5500.0, 515000.0, 1, 1, @now, @now),
('641c3742-f87f-41e3-84d0-dd2c608b3050', 'FRAPUCCINO COFFEE (CARAMEL)', '65426', @branch_id, 4, 7, 6000.0, 86073.0, 1, 1, @now, @now),
('3aae3910-1f93-4218-8106-6b19880dcb13', 'FRAPUCCINO COFFEE (CHOCOLATE)', '24988', @branch_id, 4, 7, 6000.0, 30736.5, 1, 1, @now, @now),
('832dd39b-e615-4fef-8bbf-c1252ebdd709', 'FROTHY TOP CHOCOLATE', '65027', @branch_id, 4, 7, 5500.0, 842.3, 1, 1, @now, @now),
('b3346b9a-56b7-42fe-a2b0-06d07379dd8c', 'GIN N TONIC', '98589', @branch_id, 4, 7, 5500.0, 2031186.6, 1, 1, @now, @now),
('2900ae66-4bd4-4c86-994f-ef71ac54afd8', 'HOUSE SPECIAL ORGANIC BREW', '44382', @branch_id, 4, 7, 5000.0, 5300000.0, 1, 1, @now, @now),
('9e432f49-323d-43e9-bf61-b89151f78241', 'JAMAICAN RUM PUNCH', '93244', @branch_id, 4, 7, 7500.0, 2105000.0, 1, 1, @now, @now),
('9c0d82d9-785d-48f0-b203-800b6a241344', 'LATTE COFFE', '54647', @branch_id, 4, 7, 4500.0, 624.0, 1, 1, @now, @now),
('0075645c-4771-4ee6-a598-12131f3cee73', 'LATTE COFFEE', '76594', @branch_id, 4, 7, 4500.0, 1200.0, 1, 1, @now, @now),
('9e421ffe-7d1e-4aa0-949a-2e5bc6401a2f', 'LEMON ICED TEA', '66576', @branch_id, 4, 7, 4000.0, 10255000.0, 1, 1, @now, @now),
('b8c3b2d7-9a0e-448b-ab33-f703cdc665a5', 'LONG BLACK OR AMERICANO COFFEE', '58172', @branch_id, 4, 7, 4000.0, 1200000.0, 1, 1, @now, @now),
('775d3314-085f-4f0c-812e-58044be838af', 'LONG ISLAND', '95693', @branch_id, 4, 7, 8500.0, 5050117.6, 1, 1, @now, @now),
('e8a2b5c5-f484-4341-b164-4707a2fb51a8', 'MARGARITA', '16513', @branch_id, 4, 7, 8000.0, 4635000.0, 1, 1, @now, @now),
('4090cb2a-bd54-4619-91bb-aef9d5f7d68f', 'MOCHA COFFEE', '79865', @branch_id, 4, 7, 5000.0, 627.4, 1, 1, @now, @now),
('855df3ff-ea2d-4e55-9f20-19cbea5a8757', 'PEACH ICED TEA', '96493', @branch_id, 4, 7, 4000.0, 340201.6, 1, 1, @now, @now),
('77f4ab34-51fa-4a1c-88f9-e634c0893ddc', 'SPANISH LATTE', '46460', @branch_id, 4, 7, 6000.0, 712.5, 1, 1, @now, @now),
('80448e9b-5cf5-4686-b0fb-c4fea874f121', 'STRAWBERRY MILKSHAKE', '55919', @branch_id, 4, 7, 6000.0, 487.5, 1, 1, @now, @now),
('d8946a37-de28-43e4-8ac4-67a034f94e24', 'TEQUILA SUNRISE', '67663', @branch_id, 4, 7, 7000.0, 4236000.0, 1, 1, @now, @now),
('4849743d-79b4-45a4-9439-e30f2922db94', 'VANILLA MILKSHAKE', '78012', @branch_id, 4, 7, 5500.0, 637.5, 1, 1, @now, @now),
('8a789d7d-02d8-487f-8ed0-a001c82150a2', 'VODKA SHOT', '53615', @branch_id, 4, 7, 4500.0, 695000.0, 1, 1, @now, @now),
('baed1977-7c8b-4b1c-80f4-c0c2daf65885', 'CHOCOLATE OREO DOUGHNUT', '96432', @branch_id, 2, 5, 0, 262872.0, 1, 1, @now, @now),
('a1fd1053-2779-4f69-a75b-6d54e6064265', 'STRAWBERRY JAM DOUGHNUT', '47573', @branch_id, 2, 5, 0, 585233.07, 1, 1, @now, @now),
('f34d2bf1-3220-4c38-82b4-6ba71dc200f8', 'egg production', '83666', @branch_id, 2, 5, 0, 2592.0, 1, 1, @now, @now),
('a722af19-9c26-4721-8d89-124bb63a4ed0', 'CHOCOLATE NUTELLA CROISSANT', '72340', @branch_id, 2, 5, 0, 46112.95, 1, 1, @now, @now),
('43ab340d-a4c4-4dce-a041-44e7a39ca356', 'PAIN AU CHOCOLATE CROISSANT', '66185', @branch_id, 2, 5, 0, 36870.5, 1, 1, @now, @now),
('400288af-7421-42fa-b87f-1536838dc09a', 'TUNA SANDWICH', '68505', @branch_id, 1, 5, 0, 84648.0, 1, 1, @now, @now),
('75656bae-290b-4b4d-80cc-421b2a89aae7', 'SINGLE ESPRESSO COFFEE', '44262', @branch_id, 4, 5, 0, 0.0, 1, 1, @now, @now),
('737b6c7c-16b9-46ed-be29-c97b61bb69d1', 'FRENCH TOAST WITH NUTELLA', '47568', @branch_id, 1, 5, 0, 3325000.0, 1, 1, @now, @now),
('223f3e5e-9e53-4f75-b032-90d23b19bc6b', 'CHOCOLATE SPONGE CAKE SLICE', '32632', @branch_id, 2, 5, 0, 5136.66, 1, 1, @now, @now),
('e3860c45-6a66-451b-9b3d-4450fe0f48cc', 'WAFFLES BATTER', '30740', @branch_id, 1, 5, 0, 1535.0, 1, 1, @now, @now),
('1681d44e-63e0-4297-a599-2c385fce2b5e', 'KALEMAZING SMOOTHIE', '54332', @branch_id, 4, 5, 0, 227242.0, 1, 1, @now, @now),
('ecd9cc0a-9124-454c-8747-f17836413774', 'BUTTERMILK PANCAKES', '42417', @branch_id, 1, 5, 0, 24384.01, 1, 1, @now, @now),
('05f43344-2823-44a0-911b-6a29c2e840af', 'WAFFLES WITH BUTTERMILK CHICKEN TENDERS', '85547', @branch_id, 1, 5, 0, 445120.0, 1, 1, @now, @now),
('4078c32f-b9ab-413e-9258-31e0378bb445', 'STRAWBERRY DAIQUIRI', '85057', @branch_id, 4, 5, 0, 526584.0, 1, 1, @now, @now),
('6b4dc038-9a79-47c6-849e-ed5b9652bc76', 'LEMONADE', '73635', @branch_id, 4, 5, 0, 450252.0, 1, 1, @now, @now),
('e3396605-35c3-4cbd-a4c4-a9739cff1bd3', 'Iced latte oat milk', '81593', @branch_id, 4, 5, 0, 0.0, 1, 1, @now, @now),
('52643d60-c7ef-4051-971d-65f37df149cf', 'SAUTEED MUSHROOMS', '60520', @branch_id, 1, 5, 0, 166732.0, 1, 1, @now, @now),
('2e48abcd-56e4-4873-8733-44fa12cda7cd', 'SAUTEED POTATOES', '48220', @branch_id, 1, 5, 0, 680258.1, 1, 1, @now, @now),
('898f20c6-d4e8-4a8f-8eeb-4cc60cc554f8', 'CHARGRILLED PRAWNS', '87638', @branch_id, 1, 5, 0, 524.0, 1, 1, @now, @now),
('dc926ae3-c536-46e4-aee0-0cb13e9dd62f', 'SPICY QUARTER GRILLED CHICKEN & CHIPS', '48824', @branch_id, 1, 5, 0, 23565064.0, 1, 1, @now, @now),
('55e62cc5-7a2d-41af-9c22-46f997aaaf94', 'CORNERSTORE'S MIXED GRILL PLATTER', '15300', @branch_id, 1, 5, 0, 2396628.0, 1, 1, @now, @now),
('1b1beba0-c3c9-4a58-b3a0-8cb119f136b1', 'RASPBERRY ICED TEA', '99989', @branch_id, 4, 5, 0, 675201.6, 1, 1, @now, @now),
('60a8369a-d217-4fbc-86ac-bf501023f297', 'COOKIES AND CREAM MILKSHAKE (T.O)', '23652', @branch_id, 4, 5, 0, 2087.5, 1, 1, @now, @now),
('4695a634-4af1-45d1-9e47-5ce7a29fdd70', 'SCRAMBLED EGG (BREAKFAST)', '56372', @branch_id, 1, 5, 0, 31809.25, 1, 1, @now, @now),
('d82cc57f-7a2b-4ca2-b548-1a6362aa1f47', 'THE GOAT BURGER', '68651', @branch_id, 1, 5, 0, 1334884.0, 1, 1, @now, @now),
('4b0eb91a-7492-42cb-9491-dc45746b11c5', 'OREOS CUPCAKE', '33442', @branch_id, 2, 5, 0, 2884.75, 1, 1, @now, @now),
('dd31eb6b-883a-4335-a608-bddb72d9f8a8', 'CREAM FILLING DOUGHNUT', '76333', @branch_id, 2, 5, 0, 20617.6, 1, 1, @now, @now),
('7e34e8e9-d52a-4a4b-94fb-7a1f747d5ce9', 'FRENCH TOAST WITH EGGS & BACON (SCRAMBLED)', '46046', @branch_id, 1, 5, 0, 144430.0, 1, 1, @now, @now),
('8258b1f8-416e-40f0-9557-8c7298eac3cf', 'VANILLA LATTE WITH OAT MILK', '80286', @branch_id, 4, 5, 0, 0.0, 1, 1, @now, @now),
('64c0a6da-6b94-45d1-b095-5e157ae3d50e', 'VANILLA SPONGE CAKE SLICE', '95888', @branch_id, 2, 5, 0, 19026.92, 1, 1, @now, @now),
('306fa68f-dfd3-465b-9c6e-9fc62c33802c', 'NUTTY PROFESSOR MILKSHAKE (WITH NUTS)', '50452', @branch_id, 4, 5, 0, 950150.0, 1, 1, @now, @now),
('4211e4b1-a886-489f-9a90-67ec5480bbaf', 'STRAWBERRY ICED TEA', '17175', @branch_id, 4, 5, 0, 679201.6, 1, 1, @now, @now),
('374e2f1f-b3f7-4d82-9241-720c609102d5', 'LEMONADE MELLOWTAIL', '26121', @branch_id, 4, 5, 0, 3001.68, 1, 1, @now, @now),
('691edc5f-0487-4cf2-a8a9-5de092752516', 'BIG PALMIER COOKIES', '55444', @branch_id, 2, 5, 0, 6583.37, 1, 1, @now, @now),
('1b116ad9-753c-416a-a206-9579ae30e345', 'SPIRAL COOKIES', '31918', @branch_id, 2, 5, 0, 1296.0, 1, 1, @now, @now),
('9c9f4a81-23a8-4a5d-9a70-7884144de3d2', 'DO IT FOR THE BRITS(OMELETTE)', '14098', @branch_id, 1, 5, 0, 213287.0, 1, 1, @now, @now),
('a4dfecff-babc-426e-8a49-937cc838bc4d', 'PRAWN AVOCADO SALAD', '53312', @branch_id, 1, 5, 0, 3140812.0, 1, 1, @now, @now),
('4c572b09-96db-427f-ac68-f184186d06fa', 'CORNERSTOR ESPRESSO WITH LIQUOR', '67055', @branch_id, 4, 5, 0, 250000.0, 1, 1, @now, @now),
('6447f713-269c-43e1-88cb-ac9e64ba5c46', 'TROPICAL BLAST  SMOTHIE', '78230', @branch_id, 4, 5, 0, 752730.0, 1, 1, @now, @now),
('4b45a492-581b-480c-8e68-56fdccb246e3', 'JUMBO PRAWNS WITH POTATO MASH', '81837', @branch_id, 1, 5, 0, 1234230.0, 1, 1, @now, @now),
('505d30a6-acff-473b-8e89-7aabd829551c', 'GINGER BREAD HOUSE COOKIES', '68098', @branch_id, 2, 5, 0, 131087.46, 1, 1, @now, @now),
('c4737d55-b656-446d-8b5f-3959632e4107', 'RED VELVET CUPCAKE', '92326', @branch_id, 2, 5, 0, 860.64, 1, 1, @now, @now),
('5c6de076-12a8-43c1-a299-1076ef6c251d', 'BLACK FOREST CUPCAKE', '39479', @branch_id, 2, 5, 0, 1294.45, 1, 1, @now, @now),
('8f790e9f-ba67-4e7f-a85a-edce758cca49', 'YURGY COOKIES', '90111', @branch_id, 2, 5, 0, 16570.95, 1, 1, @now, @now),
('542f1b11-89da-4de4-95d2-d1fe8666eac0', 'PANCAKES WITH WHIPPED CREAM & CHOCOLATE SAUCE', '54626', @branch_id, 1, 5, 0, 2000.0, 1, 1, @now, @now),
('66e3c0d5-5037-46db-9ea3-0f8f0b7ef818', 'CALL ME A SMOOTHSHAKE', '24626', @branch_id, 4, 5, 0, 1752838.0, 1, 1, @now, @now),
('20be8410-703c-4302-bd9e-3af4ff590682', '8 INCHES VANILLA DRY CAKE', '33802', @branch_id, 2, 5, 0, 38372.45, 1, 1, @now, @now),
('782c9ce9-5908-49c5-82a9-5f11afcd632a', 'BELGIAN THICK HOT CHOCOLATE (NEW)', '24920', @branch_id, 4, 5, 0, 1559.04, 1, 1, @now, @now),
('cd57a7a3-56de-4b4b-97b7-8aa8cb4c0a18', 'TEQUILA SHOTS', '52685', @branch_id, 4, 5, 0, 3735000.0, 1, 1, @now, @now),
('db37c230-e7c0-47e5-bdb5-8e4209b5ed4c', 'FRENCH TOAST WITH EGGS & BACON (SUNNYSIDE)', '49575', @branch_id, 1, 5, 0, 72215.0, 1, 1, @now, @now),
('6425c4ef-f2d0-4733-a8d4-64c38193329a', 'CREPES WITH CHOCOLATE SAUCE', '53243', @branch_id, 1, 5, 0, 1463.0, 1, 1, @now, @now),
('e81a2eae-06e2-4a74-aba7-06bf2f775959', 'CHOCOLATE SAUCE PRODUCTION', '78559', @branch_id, 1, 5, 0, 0.0, 1, 1, @now, @now),
('8047a201-d61b-4bf7-b742-ff7519469bd7', 'CREPES WITH CHICKEN STRIPS & MUSHROOMS', '93391', @branch_id, 1, 5, 0, 188025.0, 1, 1, @now, @now),
('3c866239-d0e2-4917-904b-a0052ed1157e', 'RED VELVET MARBLE', '13113', @branch_id, 2, 5, 0, 0.0, 1, 1, @now, @now),
('3acb54e8-382f-4189-a799-5ccad27fda71', 'ORANGE JUICE', '34033', @branch_id, 4, 5, 0, 700.0, 1, 1, @now, @now),
('3a8c423c-5123-4238-a1b5-80f973d10a37', 'PINEAPPLE ZINGER SMOTHIE', '53881', @branch_id, 4, 5, 0, 1850566.0, 1, 1, @now, @now),
('1d8ecfae-03c3-4835-b104-8b7c1fe0cde3', 'PINEAPPLE JUICE', '76430', @branch_id, 4, 5, 0, 283.0, 1, 1, @now, @now),
('4ed59b55-7cc4-47ba-a739-ea71bbacdf5d', 'FRENCH TOAST WITH ASSORTED FRUITS', '62406', @branch_id, 1, 5, 0, 150000.0, 1, 1, @now, @now),
('7a782d18-6b1d-49ee-b59f-b4c89e9770d8', 'UHT MILK (CORNERSTORE)', '50165', @branch_id, 4, 5, 0, 3000.0, 1, 1, @now, @now),
('ed8ac92c-4c76-4779-9aa1-8dc88aa83263', 'WAFFLES WITH ASSORTED FRUIT', '48703', @branch_id, 1, 5, 0, 555100.0, 1, 1, @now, @now),
('bd493087-eed3-4c18-b5e4-c18430d71788', 'MINI CROISSANT', '62981', @branch_id, 2, 5, 0, 0.0, 1, 1, @now, @now),
('a92b8147-79bd-4df1-9e11-6d2bc004f68a', 'CHOCOLATE CHIP COOKIES', '61116', @branch_id, 2, 5, 0, 2974.75, 1, 1, @now, @now),
('398fe671-9e48-4f55-ae75-6fefb95a3f6c', 'TRIO CLASSIC CAKE', '85258', @branch_id, 2, 5, 0, 257.16, 1, 1, @now, @now),
('5abd4372-98df-4d46-bbf7-f0a022d547d0', 'RED VELVET SLICE CAKE', '54910', @branch_id, 2, 5, 0, 3087.08, 1, 1, @now, @now),
('9f073233-2bbf-42da-b822-ede03c6530cc', 'WHITE FOREST SLICE', '38776', @branch_id, 2, 5, 0, 1546.46, 1, 1, @now, @now),
('670a4247-b29e-453b-b000-8dee3da807fd', 'WILDBERRIES SORBET', '92393', @branch_id, 3, 5, 0, 4501512.0, 1, 1, @now, @now),
('9a39b4c3-8a5d-4be8-b05c-c160b07132ce', 'WAFFLES WITH EGGS AND BACON (SCRAMBLED)', '65465', @branch_id, 1, 5, 0, 417215.0, 1, 1, @now, @now),
('05ba7f2d-7e8a-4387-a9fd-704defd52466', 'COOKIES & CREAM MILKSHAKE', '79059', @branch_id, 4, 5, 0, 277.5, 1, 1, @now, @now),
('d4aa7746-774f-4d46-843f-247022c20ea5', 'MATCHA LATTE N', '75498', @branch_id, 4, 5, 0, 8712.5, 1, 1, @now, @now),
('ac33da1d-345f-4cb0-944a-ff7221affbc3', 'COLESLAW (SIDE ATTRACTIONS)', '74643', @branch_id, 1, 5, 0, 249671.5, 1, 1, @now, @now),
('1297a0c7-8996-44ff-acaa-0881daadee1d', 'CLASSIC WAFFLES', '49349', @branch_id, 1, 5, 0, 415000.0, 1, 1, @now, @now),
('2aaf54ac-eb71-42cd-9621-6152d12ee88d', 'PANCAKE BATTER', '58799', @branch_id, 1, 5, 0, 861.72, 1, 1, @now, @now),
('e892e4c1-4311-44cb-8ed6-86f078448805', 'FRAPUCCINO COFFEE (STRAWBERRY)', '89949', @branch_id, 4, 5, 0, 4357952.5, 1, 1, @now, @now),
('e4dbeaf5-744e-46ee-8941-92f9e2521644', 'WAFFLE CONE', '89876', @branch_id, 2, 5, 0, 33207.0, 1, 1, @now, @now),
('ed687402-011f-477b-8809-5502c1aa83d3', 'GINGER SNAP COOKIES GELATO', '79757', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('62fc86af-c1d1-4da6-8882-e993f19a0969', 'PEANUT BUTTER GELATO', '76080', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('76591ba3-114e-4fbe-9459-2c9903b4cec2', 'HAZELNUT GELATO', '98865', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('d008908e-e2bb-4e30-ad88-fb3e70be3d3e', 'BISCOTTO GELATO', '37750', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('cc8670ce-b0b9-427a-8e88-7ef1ee1d1fe3', 'CARAMEL GELATO', '81590', @branch_id, 3, 5, 0, 720000.0, 1, 1, @now, @now),
('6bfa20f6-eef2-42c1-bbab-fbcd81fd1e81', 'VANILLA GELATO', '66082', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('3f0559f7-34d5-42af-bdef-74aea7591c0a', 'RUBY CHEESE CAKE GELATO', '91822', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('064ea612-4cde-4c9b-bdf3-c54de7c662f3', 'REDVELVET GELATO', '38750', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('65c92579-01c8-4823-9b08-174e301c62c7', 'COOKIES AND CREAM GELATO', '53828', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('a69b2e04-aeab-4163-83df-36cd26217823', 'CARAMEL CHEESE CAKE', '57857', @branch_id, 2, 5, 0, 2189599.19, 1, 1, @now, @now),
('da02d584-af91-4f48-a7f6-97265fb09582', 'CARAMEL SAUCE', '49870', @branch_id, 2, 5, 0, 1825.08, 1, 1, @now, @now),
('811f6203-0b05-4db6-8e1c-13b1003638f3', 'PRAWN PENNE PESTO', '58753', @branch_id, 1, 5, 0, 558605.0, 1, 1, @now, @now),
('2df06560-7f01-444c-aac1-a2e90bdfde7b', 'A PIECE OF NAPLES', '10190', @branch_id, 1, 5, 0, 32.0, 1, 1, @now, @now),
('ad1bc08f-f885-4d1b-a050-99243830ee03', 'MACCHIATO', '90665', @branch_id, 4, 5, 0, 1200.0, 1, 1, @now, @now),
('e1a104cb-aeb0-4c49-9428-2fb8dde5538a', 'STRAWBERRY MUM', '66482', @branch_id, 4, 5, 0, 1375850.0, 1, 1, @now, @now),
('1db57dd3-3aa9-4543-845d-f4849b4ce277', 'DO IT FOR THE BRITS (SUNNY SIDE UP)', '31001', @branch_id, 1, 5, 0, 219787.0, 1, 1, @now, @now),
('79f6804a-0714-425c-bfe3-6ffd681d0c6a', 'WAFFLE MAGIC', '29632', @branch_id, 1, 5, 0, 1000.0, 1, 1, @now, @now),
('4a1be0ad-22c1-419f-9646-f9a5b5cf73c7', 'PANCAKES WITH ASSORTED FRUITS', '94815', @branch_id, 1, 5, 0, 52000.0, 1, 1, @now, @now),
('e18163e2-2bf7-4bcc-af86-e25d3600a6f4', 'SCREW DRIVER', '53945', @branch_id, 4, 5, 0, 3530500.0, 1, 1, @now, @now),
('5eb9b859-5d59-44d2-97b7-5a04164fdf14', 'CLASSIC BACON', '91607', @branch_id, 1, 5, 0, 3390.0, 1, 1, @now, @now),
('a19bfd75-c084-4cdf-b52b-36d23913b745', 'CLASSIC FRENCH TOAST', '38265', @branch_id, 1, 5, 0, 70000.0, 1, 1, @now, @now),
('c2d1a826-7b31-49f6-b223-cf3f6671ba2c', 'MANGO GELATO', '57653', @branch_id, 3, 5, 0, 1512.0, 1, 1, @now, @now),
('83447213-9a57-452b-8999-cde9e62d66ef', 'PINEAPPLE JUICE STORE', '71716', @branch_id, 4, 5, 0, 141.5, 1, 1, @now, @now),
('95698317-fce1-4be9-984e-d21ca76186d1', 'TOAST BREAD', '48291', @branch_id, 1, 5, 0, 0.0, 1, 1, @now, @now),
('0ea05210-f6f9-46a1-85ad-4524960194ac', 'SEX IN THE DRIVEWAY', '86036', @branch_id, 4, 5, 0, 1378340.0, 1, 1, @now, @now),
('00155933-0488-467b-81ee-4d1b9bc7fa2a', 'BASE ALBA', '21239', @branch_id, 3, 5, 0, 1082312.96, 1, 1, @now, @now),
('7120f0b5-b0ac-446f-a854-5f1e0b43b451', 'CHEESY BEEF PESTO MELT', '98762', @branch_id, 1, 5, 0, 3114654.0, 1, 1, @now, @now),
('9fdc2a1e-76e4-4dee-95ac-a2e74c7c2033', 'DOLCE LATTE GELATO', '42495', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('46d8edab-b53c-4807-bf87-ba09a342d95d', 'CREAM BRULEE GELATO', '17882', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('037a1a51-89ba-462e-9cb4-2ac0adb31ebc', 'CHOCO COCONUT GELATO', '51658', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('e1dca909-c1cc-4275-987c-4bdf2af9af81', 'WHITE CHOCOLATE GELATO', '82169', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('49a31df0-57d5-4093-8c3f-c04c14f819d2', 'AMARENA CHERRY GELATO', '56306', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('e24a1e17-caad-4696-86b0-55d438b54278', 'STRACCIATELLA GELATO', '72753', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('081f3f63-8b0e-4af0-811a-7da5db73eb42', 'MACARONS', '30570', @branch_id, 2, 5, 0, 0.0, 1, 1, @now, @now),
('57ad66fc-7da9-4e73-86fb-631d324d4976', 'BROWNIES GELATO', '36481', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('332360c4-ac72-47b4-b444-ab5161a60f09', 'COFFEE GELATO', '54141', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('1857ec16-ed21-4832-ac76-01b19b150b77', 'COOKIES LEMON GELATO', '13856', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('9d971725-2241-41a8-b725-61f8802f9554', 'PANNA COTTA GELATO', '75495', @branch_id, 3, 5, 0, 120000.0, 1, 1, @now, @now),
('5c1c9b15-6828-41f1-8af4-a3fe6aa5a802', 'MARINATED CHICKEN WINGS', '35917', @branch_id, 1, 5, 0, 131303979.68, 1, 1, @now, @now),
('613ed6ba-932c-4023-801a-9347880d247b', 'MIDNIGHT CHOCOLATE GELATO', '18464', @branch_id, 3, 5, 0, 36.4, 1, 1, @now, @now),
('02285146-31cb-4100-bbe4-3b4ba12c6b21', 'VANILLA MILKSHAKE TAKE OUT', '82151', @branch_id, 4, 5, 0, 6000.0, 1, 1, @now, @now),
('27b8014a-d5db-443a-8760-44c27b849bf5', 'OMELETTE CHASSEUR', '31688', @branch_id, 1, 5, 0, 538208.0, 1, 1, @now, @now),
('a031077f-3de7-4a48-8803-0fea0469bfc8', 'CORNERSTORE'S FAMOUS AVO TOAST', '77232', @branch_id, 1, 5, 0, 510181.0, 1, 1, @now, @now),
('a01cf9b8-ddc6-44a6-8027-750895245da8', 'BAILEYS SHOT', '74622', @branch_id, 4, 5, 0, 1145000.0, 1, 1, @now, @now),
('4a422a5b-2b21-48eb-9015-44252652e13a', 'WAFFLES WITH EGGS AND BACON (SUNNYSIDE UP)', '11658', @branch_id, 1, 5, 0, 417215.0, 1, 1, @now, @now),
('bc4343c2-a3f6-4895-a133-7424a2c542b8', 'WAFFLE WITH EGGS AND BACONS', '61168', @branch_id, 1, 5, 0, 72217.24, 1, 1, @now, @now),
('66e8cb01-b6c2-4683-ba2e-6b95187e2ea2', 'Dry gin shot', '16407', @branch_id, 4, 5, 0, 645000.0, 1, 1, @now, @now),
('fe1e1718-e6b1-460c-9792-062baed0d882', 'CHAPMAN', '60492', @branch_id, 4, 5, 0, 1604585.0, 1, 1, @now, @now),
('ada1eb08-5e1c-4800-ab48-9ec4a9ce7a84', 'CORNERSTORE'S SMASHED CROISSANT BURGER', '53731', @branch_id, 1, 5, 0, 130460.0, 1, 1, @now, @now),
('3617a5ff-d947-4005-841e-4a01a29129e1', 'CORNERSTORE'S FISH KATSU BURGER', '12960', @branch_id, 1, 5, 0, 882953.0, 1, 1, @now, @now),
('7a71523a-6db2-4a4a-a0f2-c9174297005f', 'CORNERSTORE'S FESTIVE PARTY BOARD', '45751', @branch_id, 1, 5, 0, 4000.0, 1, 1, @now, @now),
('3ee0e43a-7745-4caa-ac21-ad2a67679008', 'FROZEN MARGARITA', '61509', @branch_id, 4, 5, 0, 2070050.4, 1, 1, @now, @now),
('1e9ddeea-6872-44ab-ad3f-a4efa3fefdb1', 'CHOCOLATE MOUSSE CAKE', '52901', @branch_id, 1, 5, 0, 0.0, 1, 1, @now, @now),
('41cca151-3709-4a29-91e3-ee3c8863d72c', 'FRENCH TOAST WITH BUTTERMILK CHICKEN TENDERS', '26191', @branch_id, 1, 5, 0, 675000.0, 1, 1, @now, @now),
('d4ef4c68-17ed-4a56-a0b8-deef8f2e2407', 'MAC & CHEESE WITH CRISPY CHICKEN', '76013', @branch_id, 1, 5, 0, 8291285.9, 1, 1, @now, @now),
('eaacfcf0-24d6-4e39-ab1f-3a3bdcfcb035', 'CROISSANT EGGS BENEDICT', '54260', @branch_id, 1, 5, 0, 512169.25, 1, 1, @now, @now),
('e6fe56d9-1a57-4d52-a034-abe030e0ea22', 'CORNERSTORE'S BRUNCH COMBO', '82494', @branch_id, 1, 5, 0, 523159.0, 1, 1, @now, @now),
('8506ca83-83b5-4e58-91a5-656c4f1226ca', 'PISTACHIO GELATO', '33799', @branch_id, 1, 5, 0, 0.0, 1, 1, @now, @now),
('c9169f8c-5bee-4db8-a233-fba6e1d67d89', 'CHICKEN ALFREDO PIZZA', '85981', @branch_id, 1, 5, 0, 4636250.0, 1, 1, @now, @now),
('341486ba-4c57-435c-be50-91a85ea64078', 'BUBBLE GUM GELATO', '75455', @branch_id, 3, 5, 0, 100000000.0, 1, 1, @now, @now),
('11421d88-11c9-40c1-bf42-7b0d68516592', 'CHOCOBISCOTTO GELATO', '39073', @branch_id, 3, 5, 0, 120000000.0, 1, 1, @now, @now),
('a181812c-6da6-4d09-b97d-1f63d1809d8b', 'WAFERS GELATO', '68540', @branch_id, 3, 5, 0, 120000000.0, 1, 1, @now, @now),
('481c4f99-8962-46b2-bc2d-ce13d1834b7f', 'VANILLA GELATO CORNERSTORE', '45662', @branch_id, 3, 5, 0, 120000000.0, 1, 1, @now, @now),
('e67c9c36-88dc-49b5-8d4f-d3eb32dda73f', '6 INCHES REDVELVET BENTO CAKE', '97243', @branch_id, 1, 5, 0, 0.0, 1, 1, @now, @now),
('cdcfda75-f15a-49fc-9c38-94b11a3970cf', 'FRENCH TOAST WITH STEAK EGGS BENEDICT', '63858', @branch_id, 1, 5, 0, 2437349.0, 1, 1, @now, @now),
('5f69f315-e45d-44e2-9d55-d85f7a5d3b0a', 'CORNERSTORE'S FESTIVE BRUNCH', '68487', @branch_id, 1, 5, 0, 1482904.5, 1, 1, @now, @now),
('6926b65a-7d9c-44b4-b9c0-e66385c75aba', 'CORNERSTORE'S BREAKFAST CROISSANT COMBO', '69148', @branch_id, 1, 5, 0, 512649.0, 1, 1, @now, @now),
('6a814b12-f40d-47e3-a66d-1b6555aba98f', 'HONEY GLAZED BACON', '51572', @branch_id, 1, 5, 0, 278462.0, 1, 1, @now, @now),
('3732b180-cb32-428d-8a1c-db68fa81dbe5', 'CORNERSTORE'S NEW YEAR EV PASTA WITH CHICKEN', '73183', @branch_id, 1, 5, 0, 1467695.0, 1, 1, @now, @now),
('635b28ed-57e7-4240-ba09-1437e7700ab2', 'WAFFLES WITH EGGS AND BACON (OMELETTE)', '28508', @branch_id, 1, 5, 0, 416215.0, 1, 1, @now, @now),
('a3e9a910-6d05-4905-aa82-9b941297fde4', 'AFTER EIGHT', '57359', @branch_id, 4, 5, 0, 120000000.0, 1, 1, @now, @now),
('ff335b60-ce9d-4fa0-a74b-8de8e2e0465a', 'BLUEBERRY PANCAKES', '87662', @branch_id, 1, 5, 0, 792000.0, 1, 1, @now, @now),
('64cebc56-d96d-470d-b23c-36277c4829b7', 'CORNERSTORE'S BUTTERMILK CHICKEN CROISSANT SANDO', '76048', @branch_id, 1, 5, 0, 1130635.0, 1, 1, @now, @now),
('fb6bb7e4-198b-4113-a5f4-5cbad34160d8', 'CORNERSTORE'S OREO PANCAKES', '13951', @branch_id, 1, 5, 0, 2060.0, 1, 1, @now, @now),
('d9cb8eed-cee5-4301-88fd-d52e826d4789', 'CORNERSTORE BBQ BEEF  PIZZA', '92019', @branch_id, 1, 5, 0, 4416020.0, 1, 1, @now, @now),
('c76ffbf8-7233-472b-82fe-300f7d9ec38b', 'COTTON CANDY', '99769', @branch_id, 1, 5, 0, 0.0, 1, 1, @now, @now),
('bdd9ea9d-3f53-464d-8670-3f34b8facc45', 'VALENTINE VANILLA SPONGE', '33310', @branch_id, 2, 5, 0, 1340.34, 1, 1, @now, @now),
('d598e0db-5e52-4cf4-980f-04432e46f1fa', 'VALENTINE CHOCOLATE SPONGE', '48219', @branch_id, 2, 5, 0, 11557.15, 1, 1, @now, @now);

-- ============================================================================
-- STEP 3: Recipes Summary
-- Products with their ingredients (from JSON production data)
-- ============================================================================

-- Product: BREAKFAST FOR CHAMPS (ID: 81d65978-be35-4480-be12-85b5108134b6)
--   Price: 20000.0, Cost: 899352.0, Type: 1, Sales Dept: 7
--   Ingredients (13 items):
---- AVOCADO PEAR: 1.00 PCS = 0.0
---- FRESH TOMATOES: 30.00 G = 75000.0
---- UNSALTED BUTTER: 56.00 G = 1288.0
---- VEGETABLE OIL: 20.00 ML = 64.0
---- LETTUCE: 30.00 G = 240000.0
---- CUCUMBER: 10.00 G = 70000.0
---- LOAF BREAD (WIP): 2.00 PCS = 2000.0
---- IRISH POTATO: 300.00 G = 510000.0
---- ONIONS: 15.00 G = 0.0
---- OLIVE OIL: 10.00 ML = 0.0
---- ... and 3 more ingredients

-- Product: CHICKEN SAUSAGE (ID: 286ef983-3645-474c-8693-760d1f776253)
--   Price: 8000.0, Cost: 32.0, Type: 1, Sales Dept: 7
--   Ingredients (2 items):
---- SAUSAGE: 2.00 PCS = 0.0
---- VEGETABLE OIL: 10.00 ML = 32.0

-- Product: CHICKEN WRAP (ID: 48ebc718-d766-4b19-9a64-12e476f449c0)
--   Price: 9000.0, Cost: 33088.2, Type: 1, Sales Dept: 7
--   Ingredients (7 items):
---- LETTUCE: 1.00 G = 8000.0
---- FRESH TOMATOES: 4.00 G = 10000.0
---- BIG TORTILLA BREAD: 1.00 PCS = 4585.0
---- KETCHUP & MAYO (WIP): 2.50 G = 2500.0
---- VEGETABLE OIL: 1.00 ML = 3.2
---- MARINATED CHICKEN FILLET (WIP): 8.00 G = 8000.0
---- GREEN BELL PEPPER: 4.00 G = 0.0

-- Product: CLASSIC PANCAKE (ID: c56b3cfc-c422-4494-a7f0-6e7b2b6c0a32)
--   Price: 7500.0, Cost: 71000.0, Type: 1, Sales Dept: 7
--   Ingredients (2 items):
---- PANCAKE BATTER: 1.00 PORTION = 1000.0
---- MARPLE SYRUP (WIP): 70.00 G = 70000.0

-- Product: CLASSIC SPRINGROLLS (ID: 5bde0038-3654-431f-b96a-16f6e16fc4c6)
--   Price: 8500.0, Cost: 74064.0, Type: 1, Sales Dept: 7
--   Ingredients (3 items):
---- VEGETABLE SPRINGROLL (WIP): 4.00 PCS = 4000.0
---- PEPPER SAUCE 02 (WIP): 70.00 G = 70000.0
---- VEGETABLE OIL: 20.00 ML = 64.0

-- Product: CORNERSTORE'S CHICKEN FETTUCCINE ALFREDO (ID: 1a694ddc-0460-4b3d-958a-488756b18ba8)
--   Price: 14000.0, Cost: 450976.5, Type: 1, Sales Dept: 7
--   Ingredients (10 items):
---- MARINATED CHICKEN FILLET (WIP): 150.00 G = 150000.0
---- FETTUCCINE PASTA (WIP): 100.00 G = 100000.0
---- VEGETABLE OIL: 20.00 ML = 64.0
---- SALT: 5.00 G = 2.5
---- UNSALTED BUTTER: 20.00 G = 460.0
---- PARMESSAN CHEESE: 50.00 G = 0.0
---- WHITE WINE: 20.00 ML = 0.0
---- WHITE SAUCE (WIP): 200.00 G = 200000.0
---- UHT MILK: 150.00 G = 450.0
---- MARKET CHILLI PEPPER: 5.00 G = 0.0

-- Product: CORNERSTORE'S CLUB SANDWICH (ID: 11d1139d-1e02-49c1-ac7a-00f72f2ae95f)
--   Price: 15000.0, Cost: 2489982.0, Type: 1, Sales Dept: 7
--   Ingredients (12 items):
---- MARINATED CHICKEN FILLET (WIP): 160.00 G = 160000.0
---- FRESH TOMATOES: 120.00 G = 300000.0
---- KETCHUP & MAYO (WIP): 100.00 G = 100000.0
---- BACON: 90.00 G = 2430.0
---- LETTUCE: 20.00 G = 160000.0
---- VEGETABLE OIL: 40.00 ML = 128.0
---- EGG: 2.00 PCS = 1296.0
---- LOAF BREAD (WIP): 6.00 PCS = 6000.0
---- COLESLAW (WIP): 140.00 G = 140000.0
---- FRENCH FRIES: 320.00 G = 0.0
---- ... and 2 more ingredients

-- Product: CORNERSTORE'S JUICY BURGER (ID: 76247e7d-64fe-4c45-bf21-ccb9c06f9105)
--   Price: 15000.0, Cost: 5231296.0, Type: 1, Sales Dept: 7
--   Ingredients (11 items):
---- UNSALTED BUTTER: 80.00 G = 1840.0
---- BURGER BREAD (WIP): 4.00 PCS = 4000.0
---- TOMATO KETCHUP: 40.00 G = 540000.0
---- LETTUCE: 4.00 G = 32000.0
---- FRESH TOMATOES: 200.00 G = 500000.0
---- BEEF PATTIES (WIP): 400.00 G = 400000.0
---- SLICE CHEESE: 4.00 PCS = 233200.0
---- TOMATO KETCHUP: 240.00 G = 3240000.0
---- COLESLAW (WIP): 280.00 G = 280000.0
---- FRENCH FRIES: 640.00 G = 0.0
---- ... and 1 more ingredients

-- Product: CORNERSTORE'S SEAFOOD FETTUCCINE ALFREDO (ID: 6abddd20-4a0b-4d8a-8a05-6aa27647e9b5)
--   Price: 21000.0, Cost: 555652.5, Type: 1, Sales Dept: 7
--   Ingredients (22 items):
---- SEASONED PRWANS (WIP): 200.00 PORTION = 0.0
---- FETTUCCINE PASTA (WIP): 100.00 G = 100000.0
---- UNSALTED BUTTER: 30.00 G = 690.0
---- VEGETABLE OIL: 20.00 ML = 64.0
---- PARMESSAN CHEESE: 15.00 G = 0.0
---- WHITE SAUCE (WIP): 200.00 G = 200000.0
---- UHT MILK: 150.00 G = 450.0
---- PARSLEY LEAF: 1.00 G = 4000.0
---- WHITE WINE: 20.00 ML = 0.0
---- SEASONED SHRIMPS (WIP): 80.00 G = 80000.0
---- ... and 12 more ingredients

-- Product: CORNERSTORES PASTA BOLOGNESE (ID: 6fc8ecce-0f36-4416-986c-21eb86a136bf)
--   Price: 12000.0, Cost: 308892.0, Type: 1, Sales Dept: 7
--   Ingredients (10 items):
---- SPAGHETI: 100.00 G = 105000.0
---- VEGETABLE OIL: 20.00 ML = 64.0
---- SALT: 10.00 G = 5.0
---- KNOR MAGGI SEASONING (CHICKEN&BEEF): 2.00 G = 3800.0
---- PARMESSAN CHEESE: 20.00 G = 0.0
---- OLIVE OIL: 10.00 ML = 0.0
---- BOLOGNESE SAUCE (WIP): 200.00 G = 200000.0
---- UNSALTED BUTTER: 1.00 G = 23.0
---- BAGUETTE BREAD: 0.00 PCS = 0.0
---- GREEN BELL PEPPER: 30.00 G = 0.0

-- Product: CREAMY PENNE PASTA WITH CHICKEN (ID: 393726a7-a058-4df3-a999-27be2ca86031)
--   Price: 16000.0, Cost: 859381.2, Type: 1, Sales Dept: 7
--   Ingredients (14 items):
---- MARINATED CHICKEN FILLET (WIP): 150.00 G = 150000.0
---- PENNE PASTA: 110.00 G = 554400.0
---- SALT: 10.00 G = 5.0
---- VEGETABLE OIL: 20.00 ML = 64.0
---- WHITE SAUCE (WIP): 150.00 G = 150000.0
---- UHT MILK: 150.00 G = 450.0
---- WHITE FLOUR: 2.00 G = 2.2
---- UNSALTED BUTTER: 20.00 G = 460.0
---- GARLIC: 5.00 G = 0.0
---- CAJUN SPICE SEASONING: 5.00 G = 0.0
---- ... and 4 more ingredients

-- Product: DO IT FOR THE BRITS (SCRAMBLED) (ID: c480b616-5b6f-4c01-a2d5-5582c7e7874c)
--   Price: 12500.0, Cost: 214287.0, Type: 1, Sales Dept: 7
--   Ingredients (9 items):
---- LOAF BREAD (WIP): 3.00 PCS = 3000.0
---- SAUSAGE: 2.00 PCS = 0.0
---- BAKED BEANS IN TOMATO SAUCE: 100.00 G = 0.0
---- UNSALTED BUTTER: 30.00 G = 690.0
---- MUSHROOM: 50.00 G = 83350.0
---- FRESH TOMATOES: 50.00 G = 125000.0
---- BACON: 45.00 G = 1215.0
---- VEGETABLE OIL: 10.00 ML = 32.0
---- EGG SCRAMBLED (WIP): 1.00 PCS = 1000.0

-- Product: DOUBLE AGENT BURGER (ID: 9054337f-c65e-410b-92d2-5074baa8965d)
--   Price: 17000.0, Cost: 2204948.0, Type: 1, Sales Dept: 7
--   Ingredients (11 items):
---- LETTUCE: 20.00 G = 160000.0
---- EGG: 2.00 PCS = 1296.0
---- FRENCH FRIES: 320.00 G = 0.0
---- COLESLAW (WIP): 100.00 G = 100000.0
---- TOMATO KETCHUP: 120.00 G = 1620000.0
---- ONIONS: 40.00 G = 0.0
---- SLICE CHEESE: 2.00 PCS = 116600.0
---- VEGETABLE OIL: 60.00 ML = 192.0
---- BACON: 180.00 G = 4860.0
---- BURGER BREAD (WIP): 2.00 PCS = 2000.0
---- ... and 1 more ingredients

-- Product: FRENCH FRIES (SIDE ATTRACTION) (ID: e7a32ae0-66c4-4456-9e77-55355381a57b)
--   Price: 8000.0, Cost: 950128.0, Type: 1, Sales Dept: 7
--   Ingredients (4 items):
---- FRENCH FRIES: 260.00 G = 0.0
---- TOMATO KETCHUP: 70.00 G = 945000.0
---- FRENCH FRIES SEASONING (WIP): 5.00 G = 5000.0
---- VEGETABLE OIL: 40.00 ML = 128.0

-- Product: FRENCH TOAST WITH EGGS & BACON (OMELETTE) (ID: af8fab95-897d-47a9-8b57-b33a5c034e45)
--   Price: 14500.0, Cost: 71215.0, Type: 1, Sales Dept: 7
--   Ingredients (4 items):
---- EGG OMELETTE (WIP): 1.00 PORTION = 0.0
---- FRENCH TOAST (WIP): 1.00 PCS = 0.0
---- BACON: 45.00 G = 1215.0
---- MARPLE SYRUP (WIP): 70.00 G = 70000.0

-- Product: GRILLED CHICKEN CAESAR SALAD (ID: 4a9d598b-529b-40e9-a97b-b25902144ffa)
--   Price: 15000.0, Cost: 1302332.0, Type: 1, Sales Dept: 7
--   Ingredients (9 items):
---- LETTUCE: 100.00 G = 800000.0
---- FRESH TOMATOES: 100.00 G = 250000.0
---- MARINATED CHICKEN FILLET (WIP): 150.00 G = 150000.0
---- CABBAGE: 100.00 G = 245.0
---- BACON: 45.00 G = 1215.0
---- OLIVE OIL: 20.00 ML = 0.0
---- EGG: 1.00 PCS = 648.0
---- THOUSAND ISLAND (WIP): 100.00 G = 100000.0
---- VEGETABLE OIL: 70.00 ML = 224.0

-- Product: JAMBALAYA RICE (ID: 1a7df63b-43ee-48eb-b347-ab52df31a0df)
--   Price: 13000.0, Cost: 3691097.0, Type: 1, Sales Dept: 7
--   Ingredients (12 items):
---- MARINATED CHICKEN FILLET (WIP): 140.00 G = 140000.0
---- SAUSAGE: 1.00 PCS = 0.0
---- GOLDEN SELLA PURE (BASMATI RICE): 120.00 G = 3075000.0
---- SWEET CORN: 20.00 G = 0.0
---- VEGETABLE OIL: 30.00 ML = 96.0
---- SEASONED SHRIMPS (WIP): 80.00 G = 80000.0
---- COLESLAW (WIP): 70.00 G = 70000.0
---- SALT: 2.00 G = 1.0
---- CAJUN SPICE SEASONING: 1.00 G = 0.0
---- KNOR MAGGI SEASONING (CHICKEN&BEEF): 40.00 G = 76000.0
---- ... and 2 more ingredients

-- Product: LONDON STYLED BATTERED FISH & CHIPS (ID: 5a5f05f5-1f2f-418c-9a95-cdb6ae407d28)
--   Price: 17000.0, Cost: 1024307.5, Type: 1, Sales Dept: 7
--   Ingredients (12 items):
---- WHITE FISH FILLET: 250.00 G = 0.0
---- FRENCH FRIES: 260.00 G = 0.0
---- FISH BATTER MIX: 0.00 G = 0.0
---- WHITE FLOUR: 10.00 G = 11.0
---- TOMATO KETCHUP: 70.00 G = 945000.0
---- MAYONNAISE: 70.00 G = 0.0
---- BLACK PEPPER POWDER: 1.00 G = 4200.0
---- SALT: 1.00 G = 0.5
---- LEMON: 50.00 G = 75000.0
---- OLIVE OIL: 10.00 ML = 0.0
---- ... and 2 more ingredients

-- Product: MAC AND CHEESE (ID: bec40833-fc66-4ce3-80f0-2a0d18649fc9)
--   Price: 13000.0, Cost: 2830700.5, Type: 1, Sales Dept: 7
--   Ingredients (7 items):
---- MACARONI: 80.00 G = 0.0
---- UNSALTED BUTTER: 15.00 G = 345.0
---- WHITE FLOUR: 5.00 G = 5.5
---- UHT MILK: 150.00 G = 450.0
---- WHITE SAUCE (WIP): 50.00 G = 50000.0
---- PEAK MILK: 20.00 ML = 20000.0
---- MOZZARELLA CHEESE: 50.00 G = 2759900.0

-- Product: OMELETTE (ID: 4eb1bac3-8265-463b-be84-d600697cae7b)
--   Price: 6500.0, Cost: 0.0, Type: 1, Sales Dept: 7
--   Ingredients (1 items):
---- EGG OMELETTE (WIP): 1.00 PORTION = 0.0

-- Product: SAMOSA (ID: 17bb17ae-23e9-4d09-9c61-c76b5eb9a47a)
--   Price: 8500.0, Cost: 74064.0, Type: 1, Sales Dept: 7
--   Ingredients (3 items):
---- BEEF SAMOSA (WIP): 4.00 PCS = 4000.0
---- PEPPER SAUCE 02 (WIP): 70.00 G = 70000.0
---- VEGETABLE OIL: 20.00 ML = 64.0

-- Product: SPICY BEEF WRAP (ID: 0dc44ba5-46fb-48ad-bb1b-cbfe57744297)
--   Price: 9000.0, Cost: 239544.0, Type: 1, Sales Dept: 7
--   Ingredients (6 items):
---- SEASONING BEEF (WIP): 180.00 G = 180000.0
---- VEGETABLE OIL: 40.00 ML = 128.0
---- SALT: 2.00 G = 1.0
---- BIG TORTILLA BREAD: 2.00 PCS = 9170.0
---- CABBAGE: 100.00 G = 245.0
---- KETCHUP & MAYO (WIP): 50.00 G = 50000.0

-- Product: SPICY QUARTER GRILLED CHICKEN (ID: 96c63b41-ccfd-4cef-8b68-ea6be4f30ecf)
--   Price: 15500.0, Cost: 70064.0, Type: 1, Sales Dept: 7
--   Ingredients (3 items):
---- MARINATED CHICKEN LAP (WIP): 1.00 G = 0.0
---- SWEET HOT SPICY SAUCE (WIP): 70.00 G = 70000.0
---- VEGETABLE OIL: 20.00 ML = 64.0

-- Product: SPICY WINGS (ID: c81dfc13-bc15-4476-9e41-62274a0c3ee5)
--   Price: 15500.0, Cost: 71064.0, Type: 1, Sales Dept: 7
--   Ingredients (3 items):
---- MARINATED WINGS (WIP): 1.00 PORTION = 1000.0
---- SWEET HOT SPICY SAUCE (WIP): 70.00 G = 70000.0
---- VEGETABLE OIL: 20.00 ML = 64.0

-- Product: SPICY WINGS WITH FRENCH FRIES (ID: 9023a734-1f43-4dce-a44a-d1d8c08c0073)
--   Price: 17500.0, Cost: 38750128.0, Type: 1, Sales Dept: 7
--   Ingredients (5 items):
---- CHICKEN WING: 680.00 G = 36720000.0
---- SWEET HOT SPICY SAUCE (WIP): 140.00 G = 140000.0
---- TOMATO KETCHUP: 140.00 G = 1890000.0
---- VEGETABLE OIL: 40.00 ML = 128.0
---- FRENCH FRIES: 520.00 G = 0.0

-- Product: SUNNY SIDE UP (ID: 48149c2a-3b33-44cf-b665-95483d6c053b)
--   Price: 6500.0, Cost: 5643.5, Type: 1, Sales Dept: 7
--   Ingredients (5 items):
---- EGG: 2.00 PCS = 1296.0
---- SALT: 1.00 G = 0.5
---- BLACK PEPPER POWDER: 1.00 G = 4200.0
---- UNSALTED BUTTER: 5.00 G = 115.0
---- VEGETABLE OIL: 10.00 ML = 32.0

-- Product: WAFFLES WITH WHIPPED CREAM & CHOCOLATE SAUCE (ID: cde2d005-1215-41a0-9ca0-9d84fbccadce)
--   Price: 12500.0, Cost: 345000.0, Type: 1, Sales Dept: 7
--   Ingredients (3 items):
---- WAFFLES BATTER: 345.00 G = 345000.0
---- WHIPPING CREAM: 70.00 G = 0.0
---- CHOCOLATE SAUCE PRODUCTION: 60.00 G = 0.0

-- Product: YANKEE BREAKFAST (OMELETTE) (ID: b15a01ea-b968-45b4-85d3-b8cf28f3abbf)
--   Price: 12500.0, Cost: 561064.0, Type: 1, Sales Dept: 7
--   Ingredients (9 items):
---- PANCAKE BATTER: 2.00 PORTION = 2000.0
---- SAUSAGE: 4.00 PCS = 0.0
---- BAKED BEANS IN TOMATO SAUCE: 200.00 G = 0.0
---- UNSALTED BUTTER: 100.00 G = 2300.0
---- MUSHROOM: 100.00 G = 166700.0
---- FRESH TOMATOES: 100.00 G = 250000.0
---- VEGETABLE OIL: 20.00 ML = 64.0
---- MARPLE SYRUP (WIP): 140.00 G = 140000.0
---- EGG OMELETTE (WIP): 2.00 PORTION = 0.0

-- Product: YANKEE BREAKFAST (SCRAMBLED) (ID: 7457d7c0-6cd4-4ad0-8f2e-61e30462e90c)
--   Price: 12500.0, Cost: 561914.0, Type: 1, Sales Dept: 7
--   Ingredients (9 items):
---- PANCAKE BATTER: 2.00 PORTION = 2000.0
---- SAUSAGE: 4.00 PCS = 0.0
---- BAKED BEANS IN TOMATO SAUCE: 200.00 G = 0.0
---- UNSALTED BUTTER: 50.00 G = 1150.0
---- MUSHROOM: 100.00 G = 166700.0
---- FRESH TOMATOES: 100.00 G = 250000.0
---- VEGETABLE OIL: 20.00 ML = 64.0
---- MARPLE SYRUP (WIP): 140.00 G = 140000.0
---- EGG SCRAMBLED (WIP): 2.00 PCS = 2000.0

-- Product: YANKEE BREAKFAST (SUNNYSIDE) (ID: 8173d7e5-bc75-43ab-8523-8793e9b5aed1)
--   Price: 12500.0, Cost: 281957.0, Type: 1, Sales Dept: 7
--   Ingredients (9 items):
---- PANCAKE BATTER: 1.00 PORTION = 1000.0
---- SAUSAGE: 2.00 PCS = 0.0
---- BAKED BEANS IN TOMATO SAUCE: 100.00 G = 0.0
---- UNSALTED BUTTER: 25.00 G = 575.0
---- EGG SUNNY SIDE UP (WIP): 2.00 PCS = 2000.0
---- MUSHROOM: 50.00 G = 83350.0
---- FRESH TOMATOES: 50.00 G = 125000.0
---- VEGETABLE OIL: 10.00 ML = 32.0
---- MARPLE SYRUP (WIP): 70.00 G = 70000.0

-- Product: ALMOND CROISSANT (ID: 4f0c45d1-e9fb-494f-8dfa-a87b4d9ca0cd)
--   Price: 2200.0, Cost: 0.0, Type: 2, Sales Dept: 5
--   Ingredients (3 items):
---- CALL BACK CROISSANT (WIP): 7.00 PCS = 0.0
---- FLAKE ALMOND: 70.00 G = 0.0
---- ALMOND CREAM (WIP): 350.00 G = 0.0

-- Product: BAGUETTE BREAD (ID: e504aa21-7c7f-4e73-a78b-3bf6a5a78357)
--   Price: 2500.0, Cost: 78887.5, Type: 2, Sales Dept: 5
--   Ingredients (3 items):
---- WHITE FLOUR: 1,250.00 G = 1375.0
---- SALT: 25.00 G = 12.5
---- YEAST: 25.00 G = 77500.0

-- Product: BEEF FRIAND (ID: d3763dfb-6177-45af-ac18-10e7c3f7dbd7)
--   Price: 3000.0, Cost: 5212.5, Type: 2, Sales Dept: 5
--   Ingredients (4 items):
---- SALT: 12.50 G = 6.25
---- PUFF PASTRY MARGARINE: 468.75 G = 4518.75
---- BEEF SAUCE (WIP): 1,200.00 G = 0.0
---- WHITE FLOUR: 625.00 G = 687.5

-- Product: BIG BANANA BREAD (ID: 8d9b7a63-c49c-4019-9d88-620902683f3a)
--   Price: 10000.0, Cost: 2182.06, Type: 2, Sales Dept: 5
--   Ingredients (9 items):
---- WHITE FLOUR: 308.67 G = 339.537
---- GRANULATED SUGAR: 308.67 G = 518.5656
---- EGG: 0.00 PCS = 0.0
---- GREEK YOUGHURT: 93.33 G = 298.656
---- VEGETABLE OIL: 133.33 ML = 426.656
---- BANANA FRUIT: 333.33 G = 563.3277
---- BAKING POWDER: 9.33 G = 34.9875
---- BAKING SODA: 10.67 G = 0.0
---- SALT: 0.67 G = 0.335

-- Product: BLACK FOREST SLICE (ID: 15565372-b893-47f8-aba6-315b1fd44154)
--   Price: 3500.0, Cost: 4326.3, Type: 2, Sales Dept: 6
--   Ingredients (10 items):
---- WHITE FLOUR: 60.00 G = 66.0
---- GRANULATED SUGAR: 87.50 G = 147.0
---- SALT: 1.00 G = 0.5
---- VEGETABLE OIL: 30.00 ML = 96.0
---- UHT MILK: 37.50 G = 112.5
---- EGG: 3.00 PCS = 1944.0
---- VANILLA EXTRACT: 3.00 G = 240.0
---- WHITE VINEGAR: 2.00 ML = 1000.0
---- WHIPPING CREAM: 125.00 G = 0.0
---- FRUIT COCKTAIL: 105.00 G = 720.3

-- Product: BROWNIES CHEESE CAKE (ID: 2edd0246-04e1-4dc5-8a27-c31372cd47d7)
--   Price: 7000.0, Cost: 1681.27, Type: 2, Sales Dept: 6
--   Ingredients (4 items):
---- CREAM CHEESE: 469.33 G = 0.0
---- GRANULATED SUGAR: 229.33 G = 385.2744
---- EGG: 2.00 PCS = 1296.0
---- WHIPPING CREAM: 162.00 G = 0.0

-- Product: CARAMEL CROISSANT (ID: 14ccc314-824b-4da6-b2e9-898f97bf90a8)
--   Price: 2500.0, Cost: 42612.25, Type: 2, Sales Dept: 5
--   Ingredients (7 items):
---- GRANULATED SUGAR: 63.89 G = 107.3352
---- UNSALTED BUTTER: 250.00 G = 5750.0
---- YEAST: 11.67 G = 36177.0
---- SALT: 10.00 G = 5.0
---- WHITE FLOUR: 520.83 G = 572.913
---- CARAMEL NUT (WIP): 60.00 G = 0.0
---- CARAMEL SAUCE: 100.00 ML = 0.0

-- Product: CARAMEL CUPCAKE (ID: 00b70ddb-e04b-4e40-aad4-61a0f87d3ef8)
--   Price: 3000.0, Cost: 2309.2, Type: 2, Sales Dept: 6
--   Ingredients (11 items):
---- FLOUR: 102.40 G = 112.64
---- GRANULATED SUGAR: 152.00 G = 255.36
---- EGG: 2.00 PCS = 1296.0
---- VEGETABLE OIL: 40.00 ML = 128.0
---- BAKING POWDER: 1.60 G = 6.0
---- BAKING SODA: 1.60 G = 0.0
---- SALT: 1.60 G = 0.8
---- VANILLA EXTRACT: 3.20 G = 256.0
---- UHT MILK: 84.80 G = 254.4
---- BUTTER CREAM CHOCOLATE FILLING (WIP): 160.00 G = 0.0
---- ... and 1 more ingredients

-- Product: CHEESE CROISSANT (ID: d4181b1b-beb4-4109-affd-46c19d964a33)
--   Price: 2200.0, Cost: 2806012.95, Type: 2, Sales Dept: 5
--   Ingredients (8 items):
---- GRANULATED SUGAR: 63.89 G = 107.3352
---- UNSALTED BUTTER: 250.00 G = 5750.0
---- YEAST: 11.67 G = 36177.0
---- SALT: 10.00 G = 5.0
---- EMMENTAL CHEESE: 50.00 G = 0.0
---- MOZZARELLA CHEESE: 50.00 G = 2759900.0
---- POWDERED MILK: 16.67 G = 3500.7
---- WHITE FLOUR: 520.83 G = 572.913

-- Product: CHICKEN FRIAND (ID: d86119b9-ae0e-4163-bf9c-c549ca4fa4fd)
--   Price: 3000.0, Cost: 5212.5, Type: 2, Sales Dept: 5
--   Ingredients (4 items):
---- SALT: 12.50 G = 6.25
---- PUFF PASTRY MARGARINE: 468.75 G = 4518.75
---- CHICKEN SAUCE (WIP): 1,200.00 G = 0.0
---- WHITE FLOUR: 625.00 G = 687.5

-- Product: CHOCOLATE GLAZED DOUGHNUT (ID: ffc5c2a0-37ed-4f20-88c9-39714990024f)
--   Price: 1800.0, Cost: 30940.83, Type: 2, Sales Dept: 5
--   Ingredients (8 items):
---- GRANULATED SUGAR: 47.06 G = 79.0608
---- MARGARINE BUTTER (PASTRY): 23.53 G = 115.297
---- SALT: 4.71 G = 2.355
---- YEAST: 8.24 G = 25544.0
---- CHOCOLATE GLAZE: 11.76 G = 0.0
---- WHITE FLOUR: 235.29 G = 258.819
---- POWDERED MILK: 23.53 G = 4941.3
---- egg production: 11.76 G = 0.0

-- Product: CINNAMON DOUGHNUT (ID: a002c592-e4b7-4c97-9149-70fb426270fb)
--   Price: 1500.0, Cost: 25763.73, Type: 2, Sales Dept: 5
--   Ingredients (8 items):
---- GRANULATED SUGAR: 39.22 G = 65.8896
---- MARGARINE BUTTER (PASTRY): 19.61 G = 96.089
---- SALT: 3.92 G = 1.96
---- YEAST: 6.86 G = 21266.0
---- WHITE FLOUR: 196.08 G = 215.688
---- POWDERED MILK: 19.61 G = 4118.1
---- CINNAMON SUGAR (WIP): 9.80 G = 0.0
---- egg production: 9.80 G = 0.0

-- Product: CINNAMON ROLL (ID: c9110023-a9e6-404f-a43a-a8694bcc1f73)
--   Price: 2500.0, Cost: 42000.0, Type: 2, Sales Dept: 5
--   Ingredients (3 items):
---- CINNAMON POWDER: 12.00 G = 42000.0
---- PASTRY CREAM (WIP): 150.00 G = 0.0
---- CROISSANT DOUGH CUT WASTE(WIP): 1,036.80 G = 0.0

-- Product: COCONUT BANANA BREAD (ID: ba80ac0a-0a55-4ca9-9e1b-13254872c8b3)
--   Price: 4600.0, Cost: 1550.27, Type: 2, Sales Dept: 5
--   Ingredients (10 items):
---- WHITE FLOUR: 256.67 G = 282.337
---- GRANULATED SUGAR: 256.67 G = 431.2056
---- EGG: 0.00 PCS = 0.0
---- GREEK YOUGHURT: 2.67 G = 8.544
---- VEGETABLE OIL: 110.00 ML = 352.0
---- BANANA FRUIT: 266.67 G = 450.6723
---- BAKING POWDER: 6.67 G = 25.0125
---- BAKING SODA: 8.33 G = 0.0
---- SALT: 1.00 G = 0.5
---- DESSICATED COCONUT: 66.67 G = 0.0

-- Product: COCONUT DOUGHNUT (ID: 49932ba2-7f0f-448a-9135-a76db33bd5ca)
--   Price: 1500.0, Cost: 17150.24, Type: 2, Sales Dept: 5
--   Ingredients (7 items):
---- GRANULATED SUGAR: 31.37 G = 52.7016
---- MARGARINE BUTTER (PASTRY): 15.69 G = 76.881
---- SALT: 3.14 G = 1.57
---- YEAST: 5.49 G = 17019.0
---- DESSICATED COCONUT: 7.84 G = 0.0
---- egg production: 7.84 G = 0.0
---- WHITE FLOUR: 0.08 G = 0.088

-- Product: CRISPY DOUGHNUT (ID: 5edad35e-5a68-45d9-9403-b1fc32ebc756)
--   Price: 1500.0, Cost: 15352.87, Type: 2, Sales Dept: 5
--   Ingredients (8 items):
---- GRANULATED SUGAR: 23.53 G = 39.5304
---- MARGARINE BUTTER (PASTRY): 11.76 G = 57.624
---- SALT: 2.35 G = 1.175
---- YEAST: 4.12 G = 12772.0
---- WHITE FLOUR: 11.76 G = 12.936
---- POWDERED MILK: 11.76 G = 2469.6
---- Pastry Icing: 17.65 G = 0.0
---- egg production: 5.88 G = 0.0

-- Product: HAZELNUT DOUGHNUT (ID: a5ebb899-2584-401f-a629-2379c087ad9e)
--   Price: 1500.0, Cost: 25763.73, Type: 2, Sales Dept: 5
--   Ingredients (8 items):
---- GRANULATED SUGAR: 39.22 G = 65.8896
---- MARGARINE BUTTER (PASTRY): 19.61 G = 96.089
---- YEAST: 6.86 G = 21266.0
---- SALT: 3.92 G = 1.96
---- HAZELNUT SPREAD: 9.80 G = 0.0
---- WHITE FLOUR: 196.08 G = 215.688
---- POWDERED MILK: 19.61 G = 4118.1
---- egg production: 9.80 G = 0.0

-- Product: ICING COOKIES (ID: 5e0f3fed-2c94-4361-942f-75c2efa823fc)
--   Price: 2000.0, Cost: 5105.68, Type: 2, Sales Dept: 5
--   Ingredients (7 items):
---- WHITE FLOUR: 312.00 G = 343.2
---- GRANULATED SUGAR: 180.00 G = 302.4
---- SALT: 0.96 G = 0.48
---- UNSALTED BUTTER: 160.00 G = 3680.0
---- BAKING POWDER: 0.96 G = 3.6
---- EGG: 1.00 PCS = 648.0
---- VANILLA EXTRACT: 1.60 G = 128.0

-- Product: MOCHA CAKE (ID: cf9a2014-51c2-4e51-8324-bfe0ccf4c613)
--   Price: 3500.0, Cost: 6629.4, Type: 2, Sales Dept: 6
--   Ingredients (13 items):
---- GRANULATED SUGAR: 285.00 G = 478.8
---- VEGETABLE OIL: 90.00 ML = 288.0
---- EGG: 4.00 PCS = 2592.0
---- VANILLA EXTRACT: 10.00 G = 800.0
---- GREEK YOUGHURT: 118.00 G = 377.6
---- WHITE FLOUR: 280.00 G = 308.0
---- COCOA POWDER: 55.00 G = 1760.0
---- COFFEE BEAN CAFFEINATED: 5.00 G = 0.0
---- BAKING SODA: 2.00 G = 0.0
---- SINGLE ESPRESSO COFFEE: 20.00 CU = 0.0
---- ... and 3 more ingredients

-- Product: NUTELLA BANANA BREAD (ID: d5b4b300-eb1c-45c7-9f8c-e63b5c108e2e)
--   Price: 6000.0, Cost: 1653862.76, Type: 2, Sales Dept: 5
--   Ingredients (10 items):
---- WHITE FLOUR: 85.56 G = 94.116
---- GRANULATED SUGAR: 85.56 G = 143.7408
---- EGG: 2.00 PCS = 1296.0
---- GREEK YOUGHURT: 0.89 G = 2.848
---- VEGETABLE OIL: 36.67 ML = 117.344
---- BANANA FRUIT: 88.89 G = 150.2241
---- BAKING POWDER: 2.22 G = 8.325
---- BAKING SODA: 2.78 G = 0.0
---- SALT: 0.33 G = 0.165
---- NUTELLA: 34.78 G = 1652050.0

-- Product: PLAIN CROISSANT (ID: d085f3e7-d75a-4374-a438-08297b313372)
--   Price: 2200.0, Cost: 46112.95, Type: 2, Sales Dept: 5
--   Ingredients (6 items):
---- GRANULATED SUGAR: 63.89 G = 107.3352
---- UNSALTED BUTTER: 250.00 G = 5750.0
---- YEAST: 11.67 G = 36177.0
---- SALT: 10.00 G = 5.0
---- WHITE FLOUR: 520.83 G = 572.913
---- POWDERED MILK: 16.67 G = 3500.7

-- Product: PLAIN DOUGHNUT (ID: 6f04e40a-0b24-4bb7-adae-c8805c63014d)
--   Price: 1500.0, Cost: 25763.73, Type: 2, Sales Dept: 5
--   Ingredients (7 items):
---- GRANULATED SUGAR: 39.22 G = 65.8896
---- YEAST: 6.86 G = 21266.0
---- MARGARINE BUTTER (PASTRY): 19.61 G = 96.089
---- SALT: 3.92 G = 1.96
---- WHITE FLOUR: 196.08 G = 215.688
---- POWDERED MILK: 19.61 G = 4118.1
---- egg production: 9.80 G = 0.0

-- Product: PLAIN POPCORN (ID: c162a809-d6f9-4b62-8257-a019c389b964)
--   Price: 1500.0, Cost: 7955.0, Type: 2, Sales Dept: 5
--   Ingredients (4 items):
---- RAW CORN: 1,250.00 G = 2550.0
---- VEGETABLE OIL: 562.50 ML = 1800.0
---- GRANULATED SUGAR: 687.50 G = 1155.0
---- MARGARINE BUTTER (PASTRY): 500.00 G = 2450.0

-- Product: RASPBERRY CHEESE CAKE (ID: e8026783-0f01-4f47-98ed-b4410bfc7250)
--   Price: 7000.0, Cost: 827014.92, Type: 2, Sales Dept: 6
--   Ingredients (7 items):
---- GRANULATED SUGAR: 29.17 G = 49.0056
---- DIGESTIVE BISCUIT: 80.00 G = 43333.6
---- CREAM CHEESE: 100.00 G = 0.0
---- RASPBERRY FILLING: 20.83 G = 0.0
---- GELATINE LEAF: 4.67 G = 783018.9
---- UNSALTED BUTTER: 26.67 G = 613.41
---- WHIPPING CREAM: 166.67 G = 0.0

-- Product: SAUSAGE CROISSANT (ID: 26ff1adb-502d-474c-9478-5b705b0fa6bd)
--   Price: 3000.0, Cost: 46112.95, Type: 2, Sales Dept: 5
--   Ingredients (7 items):
---- GRANULATED SUGAR: 63.89 G = 107.3352
---- UNSALTED BUTTER: 250.00 G = 5750.0
---- YEAST: 11.67 G = 36177.0
---- SALT: 10.00 G = 5.0
---- SAUSAGE: 10.00 PCS = 0.0
---- POWDERED MILK: 16.67 G = 3500.7
---- WHITE FLOUR: 520.83 G = 572.913

-- Product: SMALL BANANA BREAD (ID: 93e20426-6b32-47f8-8651-e28675911b59)
--   Price: 4600.0, Cost: 1550.27, Type: 2, Sales Dept: 5
--   Ingredients (9 items):
---- WHITE FLOUR: 256.67 G = 282.337
---- GRANULATED SUGAR: 256.67 G = 431.2056
---- EGG: 0.00 PCS = 0.0
---- GREEK YOUGHURT: 2.67 G = 8.544
---- VEGETABLE OIL: 110.00 ML = 352.0
---- BANANA FRUIT: 266.67 G = 450.6723
---- BAKING POWDER: 6.67 G = 25.0125
---- BAKING SODA: 8.33 G = 0.0
---- SALT: 1.00 G = 0.5

-- Product: SPECIAL DOUGHNUT (ID: 87d73b6c-3c7d-430e-88e9-1c61eff58704)
--   Price: 1500.0, Cost: 25769.21, Type: 2, Sales Dept: 5
--   Ingredients (8 items):
---- GRANULATED SUGAR: 39.22 G = 65.8896
---- MARGARINE BUTTER (PASTRY): 19.61 G = 96.089
---- SALT: 3.92 G = 1.96
---- YEAST: 6.86 G = 21266.0
---- ICING SUGAR: 2.45 G = 5.488
---- WHITE FLOUR: 196.08 G = 215.688
---- POWDERED MILK: 19.61 G = 4118.1
---- egg production: 9.80 G = 0.0

-- Product: STRAWBERRY CHEESE CAKE (ID: 39019df7-0ad6-4cea-89bc-faeb73c0879a)
--   Price: 5500.0, Cost: 491034.89, Type: 2, Sales Dept: 6
--   Ingredients (6 items):
---- CREAM CHEESE: 46.67 G = 0.0
---- GRANULATED SUGAR: 13.33 G = 22.3944
---- WHIPPING CREAM: 58.33 G = 0.0
---- GELATINE LEAF: 2.67 G = 447678.9
---- DIGESTIVE BISCUIT: 80.00 G = 43333.6
---- STRAWBERRY FILLING: 26.67 G = 0.0

-- Product: TRES LECHES (ID: 47d9001f-a791-420a-a215-1e1ae3963c54)
--   Price: 5500.0, Cost: 100902.68, Type: 2, Sales Dept: 5
--   Ingredients (10 items):
---- WHITE FLOUR: 53.33 G = 58.663
---- GRANULATED SUGAR: 55.56 G = 93.3408
---- SALT: 0.44 G = 0.22
---- BAKING POWDER: 0.89 G = 3.3375
---- UHT MILK: 94.44 G = 283.32
---- VANILLA EXTRACT: 1.11 G = 88.8
---- WHIPPING CREAM: 22.22 G = 0.0
---- PEAK MILK: 100.00 ML = 100000.0
---- egg production: 55.56 G = 0.0
---- CONDENSED MILK: 100.00 ML = 375.0

-- Product: VANILLA CUPCAKE (ID: 20371473-200e-41b6-904f-10700c308d97)
--   Price: 3500.0, Cost: 28262.0, Type: 2, Sales Dept: 6
--   Ingredients (9 items):
---- EGG: 8.00 PCS = 5184.0
---- VEGETABLE OIL: 400.00 ML = 1280.0
---- BAKING POWDER: 16.00 G = 60.0
---- BAKING SODA: 8.00 G = 0.0
---- SALT: 4.00 G = 2.0
---- VANILLA EXTRACT: 48.00 G = 3840.0
---- UHT MILK: 368.00 G = 1104.0
---- WHITE VINEGAR: 32.00 ML = 16000.0
---- WHITE FLOUR: 720.00 G = 792.0

-- Product: AFFOGATO COFFEE (ID: e39a9e2d-28fe-4b66-b20e-c25ef96dc8f0)
--   Price: 7000.0, Cost: 0.0, Type: 4, Sales Dept: 7
--   Ingredients (3 items):
---- COFFEE BEAN CAFFEINATED: 18.00 G = 0.0
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 1.00 RO = 0.0
---- VANILLA GELATO CORNERSTORE: 110.00 G = 0.0

-- Product: BAILEYS ESPRESSO ON THE ROCK COFFEE (ID: 84d51438-7eab-4e27-a0c8-90f292c343e6)
--   Price: 5500.0, Cost: 1100000.0, Type: 4, Sales Dept: 7
--   Ingredients (3 items):
---- COFFEE BEAN CAFFEINATED: 18.00 G = 0.0
---- BAILEYS: 50.00 ML = 1100000.0
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 1.00 RO = 0.0

-- Product: CAPPUCCINO COFFEE (ID: 1d589ffe-2dc3-471b-9fbe-d0b7d2217557)
--   Price: 4500.0, Cost: 27540.0, Type: 4, Sales Dept: 7
--   Ingredients (3 items):
---- COFFEE BEAN CAFFEINATED: 18.00 G = 0.0
---- UHT MILK: 180.00 G = 540.0
---- C-WAY WATER: 18.00 G = 27000.0

-- Product: CARAMEL HOT CHOCOLATE (ID: c8a99579-8c30-47ca-937f-8dc02538cd6a)
--   Price: 5500.0, Cost: 2284.6, Type: 4, Sales Dept: 7
--   Ingredients (5 items):
---- CONDENSED MILK: 20.00 ML = 75.0
---- HOT CHOCOLATE: 40.00 G = 109.6
---- UHT MILK: 700.00 G = 2100.0
---- CARAMEL TOPPING: 120.00 G = 0.0
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 2.00 RO = 0.0

-- Product: CARAMEL LATTE (ID: eef3ef1e-ef51-494b-8298-22abb12bb424)
--   Price: 6000.0, Cost: 600.0, Type: 4, Sales Dept: 7
--   Ingredients (4 items):
---- COFFEE BEAN CAFFEINATED: 18.00 G = 0.0
---- UHT MILK: 200.00 G = 600.0
---- CARAMEL TOPPING: 100.00 G = 0.0
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 1.00 RO = 0.0

-- Product: DOUBLE ESPRESSO COFFEE (ID: 9fd84663-b175-4c85-892c-2668e1b73184)
--   Price: 4500.0, Cost: 0.0, Type: 4, Sales Dept: 7
--   Ingredients (2 items):
---- COFFEE BEAN CAFFEINATED: 18.00 G = 0.0
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 1.00 RO = 0.0

-- Product: ESPRESSO MARTINI COFFEE (ID: cec3f0ae-6b9e-4e8c-a4b7-5b2dd4db0b1d)
--   Price: 5500.0, Cost: 515000.0, Type: 4, Sales Dept: 7
--   Ingredients (4 items):
---- COFFEE BEAN CAFFEINATED: 18.00 G = 0.0
---- VODKA: 30.00 ML = 390000.0
---- COFFEE LIQUOR: 50.00 ML = 125000.0
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 1.00 RO = 0.0

-- Product: FRAPUCCINO COFFEE (CARAMEL) (ID: 641c3742-f87f-41e3-84d0-dd2c608b3050)
--   Price: 6000.0, Cost: 86073.0, Type: 4, Sales Dept: 7
--   Ingredients (8 items):
---- COFFEE BEAN CAFFEINATED: 36.00 G = 0.0
---- CONDENSED MILK: 60.00 ML = 225.0
---- UHT MILK: 160.00 G = 480.0
---- CARAMEL TOPPING: 120.00 G = 0.0
---- C-WAY WATER: 40.00 G = 60000.0
---- POWDERED MILK: 120.00 G = 25200.0
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 2.00 RO = 0.0
---- GRANULATED SUGAR: 100.00 G = 168.0

-- Product: FRAPUCCINO COFFEE (CHOCOLATE) (ID: 3aae3910-1f93-4218-8106-6b19880dcb13)
--   Price: 6000.0, Cost: 30736.5, Type: 4, Sales Dept: 7
--   Ingredients (7 items):
---- COFFEE BEAN CAFFEINATED: 18.00 G = 0.0
---- CONDENSED MILK: 30.00 ML = 112.5
---- UHT MILK: 180.00 G = 540.0
---- C-WAY WATER: 20.00 G = 30000.0
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 1.00 RO = 0.0
---- CHOCOLATE TOPPING (I06) - NEW: 60.00 G = 0.0
---- GRANULATED SUGAR: 50.00 G = 84.0

-- Product: FROTHY TOP CHOCOLATE (ID: 832dd39b-e615-4fef-8bbf-c1252ebdd709)
--   Price: 5500.0, Cost: 842.3, Type: 4, Sales Dept: 7
--   Ingredients (5 items):
---- CONDENSED MILK: 10.00 ML = 37.5
---- HOT CHOCOLATE: 20.00 G = 54.8
---- UHT MILK: 250.00 G = 750.0
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 1.00 RO = 0.0
---- CHOCOLATE TOPPING (I06) - NEW: 60.00 G = 0.0

-- Product: GIN N TONIC (ID: b3346b9a-56b7-42fe-a2b0-06d07379dd8c)
--   Price: 5500.0, Cost: 2031186.6, Type: 4, Sales Dept: 7
--   Ingredients (3 items):
---- LEMON: 60.00 G = 90000.0
---- GORDONS GIN: 100.00 ML = 1200000.0
---- CAN SCHWEPPES: 660.00 PCS = 741186.6

-- Product: HOUSE SPECIAL ORGANIC BREW (ID: 2900ae66-4bd4-4c86-994f-ef71ac54afd8)
--   Price: 5000.0, Cost: 5300000.0, Type: 4, Sales Dept: 7
--   Ingredients (3 items):
---- GINGER: 400.00 G = 3200000.0
---- LEMON: 400.00 G = 600000.0
---- C-WAY WATER: 1,000.00 G = 1500000.0

-- Product: JAMAICAN RUM PUNCH (ID: 9e432f49-323d-43e9-bf61-b89151f78241)
--   Price: 7500.0, Cost: 2105000.0, Type: 4, Sales Dept: 7
--   Ingredients (6 items):
---- WHITE RUM: 25.00 ML = 200000.0
---- GOLD RUM: 25.00 ML = 0.0
---- CALYPSO: 25.00 ML = 125000.0
---- PINEAPPLE JUICE STORE: 50.00 ML = 740000.0
---- ORANGE JUICE (WIP): 50.00 ML = 740000.0
---- GRENADINE: 25.00 ML = 300000.0

-- Product: LATTE COFFE (ID: 9c0d82d9-785d-48f0-b203-800b6a241344)
--   Price: 4500.0, Cost: 624.0, Type: 4, Sales Dept: 7
--   Ingredients (4 items):
---- COFFEE BEAN CAFFEINATED: 18.00 G = 0.0
---- UHT MILK: 180.00 G = 540.0
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 1.00 RO = 0.0
---- GRANULATED SUGAR: 50.00 G = 84.0

-- Product: LATTE COFFEE (ID: 0075645c-4771-4ee6-a598-12131f3cee73)
--   Price: 4500.0, Cost: 1200.0, Type: 4, Sales Dept: 7
--   Ingredients (3 items):
---- COFFEE BEAN CAFFEINATED: 36.00 G = 0.0
---- UHT MILK: 400.00 G = 1200.0
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 2.00 RO = 0.0

-- Product: LEMON ICED TEA (ID: 9e421ffe-7d1e-4aa0-949a-2e5bc6401a2f)
--   Price: 4000.0, Cost: 10255000.0, Type: 4, Sales Dept: 7
--   Ingredients (4 items):
---- LEMON ICED TEA: 40.00 PCS = 160000.0
---- LEMON: 250.00 G = 375000.0
---- DRY SUGAR: 120.00 G = 9420000.0
---- C-WAY WATER: 200.00 G = 300000.0

-- Product: LONG BLACK OR AMERICANO COFFEE (ID: b8c3b2d7-9a0e-448b-ab33-f703cdc665a5)
--   Price: 4000.0, Cost: 1200000.0, Type: 4, Sales Dept: 7
--   Ingredients (3 items):
---- COFFEE BEAN CAFFEINATED: 72.00 G = 0.0
---- C-WAY WATER: 800.00 G = 1200000.0
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 4.00 RO = 0.0

-- Product: LONG ISLAND (ID: 775d3314-085f-4f0c-812e-58044be838af)
--   Price: 8500.0, Cost: 5050117.6, Type: 4, Sales Dept: 7
--   Ingredients (9 items):
---- VODKA: 50.00 ML = 650000.0
---- WHITE RUM: 50.00 ML = 400000.0
---- GIN: 50.00 CU = 0.0
---- TEQUILA: 50.00 ML = 1200000.0
---- TRIPLE SEC LIQUOR: 50.00 ML = 600000.0
---- GOLD RUM: 50.00 ML = 0.0
---- LEMON JUICE: 50.00 G = 0.0
---- PLASTIC COKE: 200.00 ML = 2200000.0
---- GRANULATED SUGAR: 70.00 G = 117.6

-- Product: MARGARITA (ID: e8a2b5c5-f484-4341-b164-4707a2fb51a8)
--   Price: 8000.0, Cost: 4635000.0, Type: 4, Sales Dept: 7
--   Ingredients (4 items):
---- DRY SUGAR: 30.00 G = 2355000.0
---- LEMON JUICE: 50.00 G = 0.0
---- TEQUILA: 70.00 ML = 1680000.0
---- TRIPLE SEC LIQUOR: 50.00 ML = 600000.0

-- Product: MOCHA COFFEE (ID: 4090cb2a-bd54-4619-91bb-aef9d5f7d68f)
--   Price: 5000.0, Cost: 627.4, Type: 4, Sales Dept: 7
--   Ingredients (5 items):
---- COFFEE BEAN CAFFEINATED: 18.00 G = 0.0
---- HOT CHOCOLATE: 10.00 G = 27.4
---- CHOCOLATE SYRUP: 30.00 G = 0.0
---- UHT MILK: 200.00 G = 600.0
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 1.00 RO = 0.0

-- Product: PEACH ICED TEA (ID: 855df3ff-ea2d-4e55-9f20-19cbea5a8757)
--   Price: 4000.0, Cost: 340201.6, Type: 4, Sales Dept: 7
--   Ingredients (4 items):
---- C-WAY WATER: 200.00 G = 300000.0
---- ICED TEA LEMON FLAVOURED: 40.00 G = 0.0
---- PEACH HEART: 80.00 CU = 40000.0
---- GRANULATED SUGAR: 120.00 G = 201.6

-- Product: SPANISH LATTE (ID: 77f4ab34-51fa-4a1c-88f9-e634c0893ddc)
--   Price: 6000.0, Cost: 712.5, Type: 4, Sales Dept: 7
--   Ingredients (4 items):
---- COFFEE BEAN CAFFEINATED: 18.00 G = 0.0
---- UHT MILK: 200.00 G = 600.0
---- CONDENSED MILK: 30.00 ML = 112.5
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 1.00 RO = 0.0

-- Product: STRAWBERRY MILKSHAKE (ID: 80448e9b-5cf5-4686-b0fb-c4fea874f121)
--   Price: 6000.0, Cost: 487.5, Type: 4, Sales Dept: 7
--   Ingredients (5 items):
---- STRAWBERRY SYRUP: 50.00 G = 0.0
---- WHIPPING CREAM: 80.00 G = 0.0
---- CONDENSED MILK: 50.00 ML = 187.5
---- UHT MILK: 100.00 G = 300.0
---- STRAWBERRY CREAM GELATO: 1.00 CU = 0.0

-- Product: TEQUILA SUNRISE (ID: d8946a37-de28-43e4-8ac4-67a034f94e24)
--   Price: 7000.0, Cost: 4236000.0, Type: 4, Sales Dept: 7
--   Ingredients (6 items):
---- TEQUILA: 100.00 ML = 2400000.0
---- GRENADINE: 44.00 ML = 528000.0
---- C-WAY WATER: 40.00 G = 60000.0
---- ORANGE JUICE: 240.00 G = 720000.0
---- LIME JUICE: 0.00 G = 0.0
---- TRIPLE SEC LIQUOR: 44.00 ML = 528000.0

-- Product: VANILLA MILKSHAKE (ID: 4849743d-79b4-45a4-9439-e30f2922db94)
--   Price: 5500.0, Cost: 637.5, Type: 4, Sales Dept: 7
--   Ingredients (4 items):
---- CONDENSED MILK: 50.00 ML = 187.5
---- UHT MILK: 150.00 G = 450.0
---- ICE BLOCK: 25.00 G = 0.0
---- VANILLA GELATO CORNERSTORE: 110.00 G = 0.0

-- Product: VODKA SHOT (ID: 8a789d7d-02d8-487f-8ed0-a001c82150a2)
--   Price: 4500.0, Cost: 695000.0, Type: 4, Sales Dept: 7
--   Ingredients (2 items):
---- VODKA: 50.00 ML = 650000.0
---- LEMON: 30.00 G = 45000.0

-- Product: CHOCOLATE OREO DOUGHNUT (ID: baed1977-7c8b-4b1c-80f4-c0c2daf65885)
--   Price: 0, Cost: 262872.0, Type: 2, Sales Dept: 5
--   Ingredients (9 items):
---- GRANULATED SUGAR: 400.00 G = 672.0
---- MARGARINE BUTTER (PASTRY): 200.00 G = 980.0
---- SALT: 40.00 G = 20.0
---- YEAST: 70.00 G = 217000.0
---- WHITE FLOUR: 2,000.00 G = 2200.0
---- POWDERED MILK: 200.00 G = 42000.0
---- OREO POWDER: 190.00 G = 0.0
---- egg production: 100.00 G = 0.0
---- DOUGHNUT GLAZE (WIP): 100.00 G = 0.0

-- Product: STRAWBERRY JAM DOUGHNUT (ID: a1fd1053-2779-4f69-a75b-6d54e6064265)
--   Price: 0, Cost: 585233.07, Type: 2, Sales Dept: 5
--   Ingredients (8 items):
---- GRANULATED SUGAR: 62.75 G = 105.42
---- MARGARINE BUTTER (PASTRY): 31.37 G = 153.713
---- SALT: 6.27 G = 3.135
---- YEAST: 10.98 G = 34038.0
---- WHITE FLOUR: 313.73 G = 345.103
---- POWDERED MILK: 31.37 G = 6587.7
---- STRAWBERRY JAM: 64.00 G = 544000.0
---- egg production: 15.69 G = 0.0

-- Product: egg production (ID: f34d2bf1-3220-4c38-82b4-6ba71dc200f8)
--   Price: 0, Cost: 2592.0, Type: 2, Sales Dept: 5
--   Ingredients (1 items):
---- EGG: 4.00 PCS = 2592.0

-- Product: CHOCOLATE NUTELLA CROISSANT (ID: a722af19-9c26-4721-8d89-124bb63a4ed0)
--   Price: 0, Cost: 46112.95, Type: 2, Sales Dept: 5
--   Ingredients (7 items):
---- GRANULATED SUGAR: 63.89 G = 107.3352
---- UNSALTED BUTTER: 250.00 G = 5750.0
---- YEAST: 11.67 G = 36177.0
---- SALT: 10.00 G = 5.0
---- HAZELNUT SPREAD: 40.00 G = 0.0
---- POWDERED MILK: 16.67 G = 3500.7
---- WHITE FLOUR: 520.83 G = 572.913

-- Product: PAIN AU CHOCOLATE CROISSANT (ID: 43ab340d-a4c4-4dce-a041-44e7a39ca356)
--   Price: 0, Cost: 36870.5, Type: 2, Sales Dept: 5
--   Ingredients (7 items):
---- GRANULATED SUGAR: 51.11 G = 85.8648
---- UNSALTED BUTTER: 200.00 G = 4600.0
---- YEAST: 9.33 G = 28923.0
---- SALT: 8.00 G = 4.0
---- POWDERED MILK: 13.33 G = 2799.3
---- WHITE FLOUR: 416.67 G = 458.337
---- CHOCOLATE STICK: 120.00 G = 0.0

-- Product: TUNA SANDWICH (ID: 400288af-7421-42fa-b87f-1536838dc09a)
--   Price: 0, Cost: 84648.0, Type: 1, Sales Dept: 5
--   Ingredients (5 items):
---- LOAF BREAD (WIP): 4.00 PCS = 4000.0
---- EGG: 1.00 PCS = 648.0
---- FLAKE TUNA FISH: 50.00 G = 0.0
---- MAYONNAISE: 40.00 G = 0.0
---- LETTUCE: 10.00 G = 80000.0

-- Product: SINGLE ESPRESSO COFFEE (ID: 75656bae-290b-4b4d-80cc-421b2a89aae7)
--   Price: 0, Cost: 0.0, Type: 4, Sales Dept: 5
--   Ingredients (2 items):
---- COFFEE BEAN CAFFEINATED: 18.00 G = 0.0
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 2.00 RO = 0.0

-- Product: FRENCH TOAST WITH NUTELLA (ID: 737b6c7c-16b9-46ed-be29-c97b61bb69d1)
--   Price: 0, Cost: 3325000.0, Type: 1, Sales Dept: 5
--   Ingredients (2 items):
---- FRENCH TOAST (WIP): 1.00 PCS = 0.0
---- NUTELLA: 70.00 G = 3325000.0

-- Product: CHOCOLATE SPONGE CAKE SLICE (ID: 223f3e5e-9e53-4f75-b032-90d23b19bc6b)
--   Price: 0, Cost: 5136.66, Type: 2, Sales Dept: 5
--   Ingredients (10 items):
---- WHITE FLOUR: 148.89 G = 163.779
---- GRANULATED SUGAR: 26.67 G = 44.8056
---- SALT: 2.00 G = 1.0
---- WHITE VINEGAR: 4.00 ML = 2000.0
---- VANILLA EXTRACT: 4.00 G = 320.0
---- VEGETABLE OIL: 119.11 ML = 381.152
---- BUTTER CREAM (WIP): 69.56 G = 0.0
---- BAKING SODA: 4.00 G = 0.0
---- COCOA POWDER: 69.56 G = 2225.92
---- BUTTER MILK (WIP): 222.22 G = 0.0

-- Product: WAFFLES BATTER (ID: e3860c45-6a66-451b-9b3d-4450fe0f48cc)
--   Price: 0, Cost: 1535.0, Type: 1, Sales Dept: 5
--   Ingredients (4 items):
---- UHT MILK: 320.00 G = 960.0
---- UNSALTED BUTTER: 25.00 G = 575.0
---- WAFFLE MIX (WIP): 660.00 G = 0.0
---- egg production: 100.00 G = 0.0

-- Product: KALEMAZING SMOOTHIE (ID: 1681d44e-63e0-4297-a599-2c385fce2b5e)
--   Price: 0, Cost: 227242.0, Type: 4, Sales Dept: 5
--   Ingredients (7 items):
---- PINEAPPLE FRUIT: 250.00 G = 1415.0
---- C-WAY WATER: 150.00 G = 225000.0
---- PEANUT TOPPING: 100.00 G = 0.0
---- GREEK YOUGHURT: 100.00 G = 320.0
---- BANANA FRUIT: 300.00 G = 507.0
---- ALMOND MILK: 25.00 G = 0.0
---- KALE LEAVE: 2.00 G = 0.0

-- Product: BUTTERMILK PANCAKES (ID: ecd9cc0a-9124-454c-8747-f17836413774)
--   Price: 0, Cost: 24384.01, Type: 1, Sales Dept: 5
--   Ingredients (7 items):
---- GREEK YOUGHURT: 20.00 G = 64.0
---- VANILLA EXTRACT: 0.67 G = 53.6
---- UHT MILK: 45.00 G = 135.0
---- EGG: 1.00 PCS = 648.0
---- UNSALTED BUTTER: 6.67 G = 153.41
---- MARPLE SYRUP (WIP): 23.33 G = 23330.0
---- BUTTERMILK PANCAKE MIX (WIP): 56.00 G = 0.0

-- Product: WAFFLES WITH BUTTERMILK CHICKEN TENDERS (ID: 05f43344-2823-44a0-911b-6a29c2e840af)
--   Price: 0, Cost: 445120.0, Type: 1, Sales Dept: 5
--   Ingredients (4 items):
---- WAFFLES BATTER: 345.00 G = 345000.0
---- MARINATED CHICKEN FILLET (WIP): 100.00 G = 100000.0
---- UHT MILK: 40.00 G = 120.0
---- CHICKEN TENDER MIX (WIP): 77.00 G = 0.0

-- Product: STRAWBERRY DAIQUIRI (ID: 4078c32f-b9ab-413e-9258-31e0378bb445)
--   Price: 0, Cost: 526584.0, Type: 4, Sales Dept: 5
--   Ingredients (5 items):
---- STRAWBERRY SYRUP: 25.00 G = 0.0
---- STRAWBERRY: 16.00 G = 104000.0
---- LEMON: 15.00 G = 22500.0
---- WHITE RUM: 50.00 ML = 400000.0
---- GRANULATED SUGAR: 50.00 G = 84.0

-- Product: LEMONADE (ID: 6b4dc038-9a79-47c6-849e-ed5b9652bc76)
--   Price: 0, Cost: 450252.0, Type: 4, Sales Dept: 5
--   Ingredients (3 items):
---- C-WAY WATER: 200.00 G = 300000.0
---- LEMON: 100.00 G = 150000.0
---- GRANULATED SUGAR: 150.00 G = 252.0

-- Product: Iced latte oat milk (ID: e3396605-35c3-4cbd-a4c4-a9739cff1bd3)
--   Price: 0, Cost: 0.0, Type: 4, Sales Dept: 5
--   Ingredients (2 items):
---- OAT MILK: 200.00 G = 0.0
---- ICE: 150.00 G = 0.0

-- Product: SAUTEED MUSHROOMS (ID: 52643d60-c7ef-4051-971d-65f37df149cf)
--   Price: 0, Cost: 166732.0, Type: 1, Sales Dept: 5
--   Ingredients (2 items):
---- MUSHROOM: 100.00 G = 166700.0
---- VEGETABLE OIL: 10.00 ML = 32.0

-- Product: SAUTEED POTATOES (ID: 2e48abcd-56e4-4873-8733-44fa12cda7cd)
--   Price: 0, Cost: 680258.1, Type: 1, Sales Dept: 5
--   Ingredients (5 items):
---- IRISH POTATO: 400.00 G = 680000.0
---- UNSALTED BUTTER: 10.00 G = 230.0
---- VEGETABLE OIL: 8.00 ML = 25.6
---- SALT: 5.00 G = 2.5
---- GREEN BELL PEPPER: 50.00 G = 0.0

-- Product: CHARGRILLED PRAWNS (ID: 898f20c6-d4e8-4a8f-8eeb-4cc60cc554f8)
--   Price: 0, Cost: 524.0, Type: 1, Sales Dept: 5
--   Ingredients (3 items):
---- SEASONED PRWANS (WIP): 1.00 PORTION = 0.0
---- VEGETABLE OIL: 20.00 ML = 64.0
---- UNSALTED BUTTER: 20.00 G = 460.0

-- Product: SPICY QUARTER GRILLED CHICKEN & CHIPS (ID: dc926ae3-c536-46e4-aee0-0cb13e9dd62f)
--   Price: 0, Cost: 23565064.0, Type: 1, Sales Dept: 5
--   Ingredients (5 items):
---- CHICKEN LAP: 500.00 G = 22500000.0
---- SWEET HOT SPICY SAUCE (WIP): 120.00 G = 120000.0
---- VEGETABLE OIL: 20.00 ML = 64.0
---- FRENCH FRIES: 260.00 G = 0.0
---- TOMATO KETCHUP: 70.00 G = 945000.0

-- Product: CORNERSTORE'S MIXED GRILL PLATTER (ID: 55e62cc5-7a2d-41af-9c22-46f997aaaf94)
--   Price: 0, Cost: 2396628.0, Type: 1, Sales Dept: 5
--   Ingredients (12 items):
---- CHICKEN KEEBAB (WIP): 1.00 PORTION = 1000.0
---- SEASONED PRWANS (WIP): 1.00 PORTION = 0.0
---- BEEF SUYA (WIP): 1.00 PORTION = 1000.0
---- WHITE SAUCE (WIP): 1.00 G = 1000.0
---- SAUSAGE: 2.00 PCS = 0.0
---- GOLDEN SELLA PURE (BASMATI RICE): 60.00 G = 1537500.0
---- WHITE FISH FILLET: 200.00 G = 0.0
---- CARROT: 30.00 G = 45000.0
---- PEPPER SAUCE 02 (WIP): 1.00 G = 1000.0
---- VEGETABLE OIL: 40.00 ML = 128.0
---- ... and 2 more ingredients

-- Product: RASPBERRY ICED TEA (ID: 1b1beba0-c3c9-4a58-b3a0-8cb119f136b1)
--   Price: 0, Cost: 675201.6, Type: 4, Sales Dept: 5
--   Ingredients (5 items):
---- LEMON: 250.00 G = 375000.0
---- C-WAY WATER: 200.00 G = 300000.0
---- ICED TEA LEMON FLAVOURED: 40.00 G = 0.0
---- WILDBERRIES TOPPING (I27): 80.00 G = 0.0
---- GRANULATED SUGAR: 120.00 G = 201.6

-- Product: COOKIES AND CREAM MILKSHAKE (T.O) (ID: 60a8369a-d217-4fbc-86ac-bf501023f297)
--   Price: 0, Cost: 2087.5, Type: 4, Sales Dept: 5
--   Ingredients (5 items):
---- CHOCOLATE TOPPING (I06) - NEW: 50.00 G = 0.0
---- OREOS: 2.00 G = 1600.0
---- CONDENSED MILK: 50.00 ML = 187.5
---- UHT MILK: 100.00 G = 300.0
---- VANILLA GELATO CORNERSTORE: 110.00 G = 0.0

-- Product: SCRAMBLED EGG (BREAKFAST) (ID: 4695a634-4af1-45d1-9e47-5ce7a29fdd70)
--   Price: 0, Cost: 31809.25, Type: 1, Sales Dept: 5
--   Ingredients (7 items):
---- EGG: 3.00 PCS = 1944.0
---- UHT MILK: 30.00 G = 90.0
---- UNSALTED BUTTER: 25.00 G = 575.0
---- SPRING ONIONS: 10.00 G = 25000.0
---- BLACK PEPPER POWDER: 1.00 G = 4200.0
---- PARMESSAN CHEESE: 10.00 G = 0.0
---- SALT: 0.50 G = 0.25

-- Product: THE GOAT BURGER (ID: d82cc57f-7a2b-4ca2-b548-1a6362aa1f47)
--   Price: 0, Cost: 1334884.0, Type: 1, Sales Dept: 5
--   Ingredients (14 items):
---- BURGER BREAD (WIP): 1.00 PCS = 1000.0
---- KETCHUP & MAYO (WIP): 45.00 G = 45000.0
---- GOAT PATTIES (WIP): 100.00 G = 100000.0
---- LETTUCE: 10.00 G = 80000.0
---- FRESH TOMATOES: 40.00 G = 100000.0
---- CUCUMBER: 20.00 G = 140000.0
---- ONIONS: 90.00 G = 0.0
---- BROWN SUGAR: 20.00 G = 60.0
---- REAL BALSAMIC VINEGAR: 20.00 ML = 0.0
---- FRENCH FRIES: 160.00 G = 0.0
---- ... and 4 more ingredients

-- Product: OREOS CUPCAKE (ID: 4b0eb91a-7492-42cb-9491-dc45746b11c5)
--   Price: 0, Cost: 2884.75, Type: 2, Sales Dept: 5
--   Ingredients (9 items):
---- VEGETABLE OIL: 50.00 ML = 160.0
---- BAKING POWDER: 2.00 G = 7.5
---- BAKING SODA: 1.00 G = 0.0
---- SALT: 0.50 G = 0.25
---- VANILLA EXTRACT: 6.00 G = 480.0
---- UHT MILK: 46.00 G = 138.0
---- WHITE VINEGAR: 4.00 ML = 2000.0
---- WHITE FLOUR: 90.00 G = 99.0
---- OREO POWDER: 120.00 G = 0.0

-- Product: CREAM FILLING DOUGHNUT (ID: dd31eb6b-883a-4335-a608-bddb72d9f8a8)
--   Price: 0, Cost: 20617.6, Type: 2, Sales Dept: 5
--   Ingredients (8 items):
---- GRANULATED SUGAR: 31.37 G = 52.7016
---- MARGARINE BUTTER (PASTRY): 15.69 G = 76.881
---- SALT: 3.14 G = 1.57
---- YEAST: 5.49 G = 17019.0
---- WHITE FLOUR: 156.86 G = 172.546
---- POWDERED MILK: 15.69 G = 3294.9
---- egg production: 7.84 G = 0.0
---- PASTRY CREAM (WIP): 32.00 G = 0.0

-- Product: FRENCH TOAST WITH EGGS & BACON (SCRAMBLED) (ID: 7e34e8e9-d52a-4a4b-94fb-7a1f747d5ce9)
--   Price: 0, Cost: 144430.0, Type: 1, Sales Dept: 5
--   Ingredients (4 items):
---- FRENCH TOAST (WIP): 2.00 PCS = 0.0
---- BACON: 90.00 G = 2430.0
---- EGG SCRAMBLED (WIP): 2.00 PCS = 2000.0
---- MARPLE SYRUP (WIP): 140.00 G = 140000.0

-- Product: VANILLA LATTE WITH OAT MILK (ID: 8258b1f8-416e-40f0-9557-8c7298eac3cf)
--   Price: 0, Cost: 0.0, Type: 4, Sales Dept: 5
--   Ingredients (3 items):
---- COFFEE BEAN DECAF: 16.00 G = 0.0
---- VANILLA FLAVOUR SYRUP: 20.00 G = 0.0
---- OAT MILK: 200.00 G = 0.0

-- Product: VANILLA SPONGE CAKE SLICE (ID: 64c0a6da-6b94-45d1-b095-5e157ae3d50e)
--   Price: 0, Cost: 19026.92, Type: 2, Sales Dept: 5
--   Ingredients (9 items):
---- WHITE FLOUR: 666.67 G = 733.337
---- GRANULATED SUGAR: 600.00 G = 1008.0
---- UHT MILK: 213.33 G = 639.99
---- SALT: 10.67 G = 5.335
---- WHITE VINEGAR: 16.00 ML = 8000.0
---- VANILLA EXTRACT: 10.67 G = 853.6
---- VEGETABLE OIL: 133.33 ML = 426.656
---- UNSALTED BUTTER: 320.00 G = 7360.0
---- BUTTER CREAM (WIP): 488.89 G = 0.0

-- Product: NUTTY PROFESSOR MILKSHAKE (WITH NUTS) (ID: 306fa68f-dfd3-465b-9c6e-9fc62c33802c)
--   Price: 0, Cost: 950150.0, Type: 4, Sales Dept: 5
--   Ingredients (5 items):
---- NUTELLA: 20.00 G = 950000.0
---- CONDENSED MILK: 40.00 ML = 150.0
---- WHIPPING CREAM: 50.00 G = 0.0
---- PEANUT TOPPING: 20.00 G = 0.0
---- VANILLA GELATO CORNERSTORE: 110.00 G = 0.0

-- Product: STRAWBERRY ICED TEA (ID: 4211e4b1-a886-489f-9a90-67ec5480bbaf)
--   Price: 0, Cost: 679201.6, Type: 4, Sales Dept: 5
--   Ingredients (5 items):
---- LEMON: 250.00 G = 375000.0
---- C-WAY WATER: 200.00 G = 300000.0
---- FRAGOLA  (STRAWBERRY ) TOPPING (I07): 80.00 G = 0.0
---- WILDBERRIES TOPPING (I27): 40.00 G = 0.0
---- GRANULATED SUGAR: 120.00 G = 201.6

-- Product: LEMONADE MELLOWTAIL (ID: 374e2f1f-b3f7-4d82-9241-720c609102d5)
--   Price: 0, Cost: 3001.68, Type: 4, Sales Dept: 5
--   Ingredients (3 items):
---- C-WAY WATER: 1.00 G = 1500.0
---- GRANULATED SUGAR: 1.00 G = 1.68
---- LEMON: 1.00 G = 1500.0

-- Product: BIG PALMIER COOKIES (ID: 691edc5f-0487-4cf2-a8a9-5de092752516)
--   Price: 0, Cost: 6583.37, Type: 2, Sales Dept: 5
--   Ingredients (3 items):
---- SALT: 11.11 G = 5.555
---- PUFF PASTRY MARGARINE: 555.56 G = 5355.5984
---- WHITE FLOUR: 1,111.11 G = 1222.221

-- Product: SPIRAL COOKIES (ID: 1b116ad9-753c-416a-a206-9579ae30e345)
--   Price: 0, Cost: 1296.0, Type: 2, Sales Dept: 5
--   Ingredients (3 items):
---- SPIRAL CHOCOLATE DOUGH (WIP): 625.00 G = 0.0
---- SPIRAL VANILLA DOUGH: 625.00 G = 0.0
---- EGG: 2.00 PCS = 1296.0

-- Product: DO IT FOR THE BRITS(OMELETTE) (ID: 9c9f4a81-23a8-4a5d-9a70-7884144de3d2)
--   Price: 0, Cost: 213287.0, Type: 1, Sales Dept: 5
--   Ingredients (9 items):
---- LOAF BREAD (WIP): 3.00 PCS = 3000.0
---- SAUSAGE: 2.00 PCS = 0.0
---- BAKED BEANS IN TOMATO SAUCE: 100.00 G = 0.0
---- UNSALTED BUTTER: 30.00 G = 690.0
---- MUSHROOM: 50.00 G = 83350.0
---- FRESH TOMATOES: 50.00 G = 125000.0
---- BACON: 45.00 G = 1215.0
---- VEGETABLE OIL: 10.00 ML = 32.0
---- EGG OMELETTE (WIP): 1.00 PORTION = 0.0

-- Product: PRAWN AVOCADO SALAD (ID: a4dfecff-babc-426e-8a49-937cc838bc4d)
--   Price: 0, Cost: 3140812.0, Type: 1, Sales Dept: 5
--   Ingredients (7 items):
---- LETTUCE: 200.00 G = 1600000.0
---- CABBAGE: 200.00 G = 490.0
---- SEASONED PRWANS (WIP): 2.00 PORTION = 0.0
---- THOUSAND ISLAND (WIP): 200.00 G = 200000.0
---- CUCUMBER: 120.00 G = 840000.0
---- FRESH TOMATOES: 200.00 G = 500000.0
---- UNSALTED BUTTER: 14.00 G = 322.0

-- Product: CORNERSTOR ESPRESSO WITH LIQUOR (ID: 4c572b09-96db-427f-ac68-f184186d06fa)
--   Price: 0, Cost: 250000.0, Type: 4, Sales Dept: 5
--   Ingredients (3 items):
---- COFFEE BEAN CAFFEINATED: 18.00 G = 0.0
---- CALYPSO: 50.00 ML = 250000.0
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 1.00 RO = 0.0

-- Product: TROPICAL BLAST  SMOTHIE (ID: 6447f713-269c-43e1-88cb-ac9e64ba5c46)
--   Price: 0, Cost: 752730.0, Type: 4, Sales Dept: 5
--   Ingredients (6 items):
---- PINEAPPLE FRUIT: 300.00 G = 1698.0
---- ORANGE: 3.00 PCS = 525.0
---- C-WAY WATER: 500.00 G = 750000.0
---- MANGO FRUIT: 120.00 PCS = 0.0
---- BANANA FRUIT: 300.00 G = 507.0
---- ALMOND MILK: 25.00 G = 0.0

-- Product: JUMBO PRAWNS WITH POTATO MASH (ID: 4b45a492-581b-480c-8e68-56fdccb246e3)
--   Price: 0, Cost: 1234230.0, Type: 1, Sales Dept: 5
--   Ingredients (11 items):
---- IRISH POTATO: 600.00 G = 1020000.0
---- UHT MILK: 200.00 G = 600.0
---- UNSALTED BUTTER: 100.00 G = 2300.0
---- CARROT: 50.00 G = 75000.0
---- BLACK PEPPER POWDER: 2.00 G = 8400.0
---- HOISIN SAUCE: 2.00 G = 7000.0
---- SALT: 4.00 G = 2.0
---- SLICE CHEESE: 2.00 PCS = 116600.0
---- VEGETABLE OIL: 40.00 ML = 128.0
---- CORN FLOUR: 4.00 G = 4200.0
---- ... and 1 more ingredients

-- Product: GINGER BREAD HOUSE COOKIES (ID: 505d30a6-acff-473b-8e89-7aabd829551c)
--   Price: 0, Cost: 131087.46, Type: 2, Sales Dept: 5
--   Ingredients (7 items):
---- WHITE FLOUR: 1.11 G = 1.221
---- BAKING POWDER: 6.16 G = 23.1
---- GRANULATED SUGAR: 1.16 G = 1.9488
---- SALT: 0.50 G = 0.25
---- VANILLA EXTRACT: 12.00 G = 960.0
---- UNSALTED BUTTER: 21.78 G = 500.94
---- EGG: 200.00 PCS = 129600.0

-- Product: RED VELVET CUPCAKE (ID: c4737d55-b656-446d-8b5f-3959632e4107)
--   Price: 0, Cost: 860.64, Type: 2, Sales Dept: 5
--   Ingredients (10 items):
---- GRANULATED SUGAR: 17.80 G = 29.904
---- RED SUGAR FLAIR: 0.72 G = 0.0
---- COCOA POWDER: 0.20 G = 6.4
---- SALT: 0.24 G = 0.12
---- BAKING SODA: 0.28 G = 0.0
---- EGG: 1.00 PCS = 648.0
---- WHITE VINEGAR: 0.24 ML = 120.0
---- UHT MILK: 12.80 G = 38.4
---- WHITE FLOUR: 16.20 G = 17.82
---- CREAM CHEESE ICING (WIP): 20.00 G = 0.0

-- Product: BLACK FOREST CUPCAKE (ID: 5c6de076-12a8-43c1-a299-1076ef6c251d)
--   Price: 0, Cost: 1294.45, Type: 2, Sales Dept: 5
--   Ingredients (10 items):
---- GRANULATED SUGAR: 95.00 G = 159.6
---- EGG: 1.00 PCS = 648.0
---- VEGETABLE OIL: 25.00 ML = 80.0
---- BAKING POWDER: 1.00 G = 3.75
---- BAKING SODA: 1.00 G = 0.0
---- SALT: 1.00 G = 0.5
---- VANILLA EXTRACT: 2.00 G = 160.0
---- UHT MILK: 53.00 G = 159.0
---- BUTTER CREAM CHOCOLATE FILLING (WIP): 100.00 G = 0.0
---- WHITE FLOUR: 76.00 G = 83.6

-- Product: YURGY COOKIES (ID: 8f790e9f-ba67-4e7f-a85a-edce758cca49)
--   Price: 0, Cost: 16570.95, Type: 2, Sales Dept: 5
--   Ingredients (6 items):
---- WHITE FLOUR: 550.00 G = 605.0
---- SALT: 5.50 G = 2.75
---- UNSALTED BUTTER: 412.50 G = 9487.5
---- BROWN SUGAR: 192.50 G = 577.5
---- ICING SUGAR: 55.00 G = 123.2
---- POWDERED MILK: 27.50 G = 5775.0

-- Product: PANCAKES WITH WHIPPED CREAM & CHOCOLATE SAUCE (ID: 542f1b11-89da-4de4-95d2-d1fe8666eac0)
--   Price: 0, Cost: 2000.0, Type: 1, Sales Dept: 5
--   Ingredients (4 items):
---- PANCAKE BATTER: 2.00 PORTION = 2000.0
---- WHIPPING CREAM: 23.33 G = 0.0
---- egg production: 0.33 G = 0.0
---- CHOCOLATE SAUCE PRODUCTION: 20.00 G = 0.0

-- Product: CALL ME A SMOOTHSHAKE (ID: 66e3c0d5-5037-46db-9ea3-0f8f0b7ef818)
--   Price: 0, Cost: 1752838.0, Type: 4, Sales Dept: 5
--   Ingredients (7 items):
---- PINEAPPLE JUICE STORE: 50.00 ML = 740000.0
---- C-WAY WATER: 400.00 G = 600000.0
---- BANANA FRUIT: 200.00 G = 338.0
---- AVOCADO PEAR: 35.00 PCS = 0.0
---- BLUE BERRY: 50.00 G = 325000.0
---- CINNAMON POWDER: 25.00 G = 87500.0
---- SPINACH: 100.00 G = 0.0

-- Product: 8 INCHES VANILLA DRY CAKE (ID: 20be8410-703c-4302-bd9e-3af4ff590682)
--   Price: 0, Cost: 38372.45, Type: 2, Sales Dept: 5
--   Ingredients (10 items):
---- WHITE FLOUR: 1,800.00 G = 1980.0
---- GRANULATED SUGAR: 1,620.00 G = 2721.6
---- SALT: 17.00 G = 8.5
---- BAKING POWDER: 29.00 G = 108.75
---- VEGETABLE OIL: 720.00 ML = 2304.0
---- MARGARINE BUTTER (PASTRY): 504.00 G = 2469.6
---- VANILLA EXTRACT: 29.00 G = 2320.0
---- BOREHOLE WATER: 450.00 G = 0.0
---- POWDERED MILK: 126.00 G = 26460.0
---- AMERICAN ICING (WIP): 2,100.00 G = 0.0

-- Product: BELGIAN THICK HOT CHOCOLATE (NEW) (ID: 782c9ce9-5908-49c5-82a9-5f11afcd632a)
--   Price: 0, Cost: 1559.04, Type: 4, Sales Dept: 5
--   Ingredients (7 items):
---- UHT MILK: 118.00 G = 354.0
---- WHIPPING CREAM: 79.00 G = 0.0
---- CHOCOLATE CHUNK: 56.00 G = 0.0
---- ESPRESSO POWDER: 3.00 G = 0.0
---- VANILLA EXTRACT: 15.00 G = 1200.0
---- GRANULATED SUGAR: 1.00 G = 1.68
---- GRANULATED SUGAR: 2.00 G = 3.36

-- Product: TEQUILA SHOTS (ID: cd57a7a3-56de-4b4b-97b7-8aa8cb4c0a18)
--   Price: 0, Cost: 3735000.0, Type: 4, Sales Dept: 5
--   Ingredients (2 items):
---- LEMON: 90.00 G = 135000.0
---- TEQUILA: 150.00 ML = 3600000.0

-- Product: FRENCH TOAST WITH EGGS & BACON (SUNNYSIDE) (ID: db37c230-e7c0-47e5-bdb5-8e4209b5ed4c)
--   Price: 0, Cost: 72215.0, Type: 1, Sales Dept: 5
--   Ingredients (4 items):
---- BACON: 45.00 G = 1215.0
---- MARPLE SYRUP (WIP): 70.00 G = 70000.0
---- FRENCH TOAST (WIP): 1.00 PCS = 0.0
---- EGG SUNNY SIDE UP (WIP): 1.00 PCS = 1000.0

-- Product: CREPES WITH CHOCOLATE SAUCE (ID: 6425c4ef-f2d0-4733-a8d4-64c38193329a)
--   Price: 0, Cost: 1463.0, Type: 1, Sales Dept: 5
--   Ingredients (6 items):
---- Sweet crepes mix ( WIP ): 1.00 PORTION = 0.0
---- UNSALTED BUTTER: 15.00 G = 345.0
---- EGG: 1.00 PCS = 648.0
---- VANILLA EXTRACT: 1.00 G = 80.0
---- UHT MILK: 130.00 G = 390.0
---- CHOCOLATE SAUCE PRODUCTION: 70.00 G = 0.0

-- Product: CHOCOLATE SAUCE PRODUCTION (ID: e81a2eae-06e2-4a74-aba7-06bf2f775959)
--   Price: 0, Cost: 0.0, Type: 1, Sales Dept: 5
--   Ingredients (1 items):
---- CHOCOLATE GLAZE: 500.00 G = 0.0

-- Product: CREPES WITH CHICKEN STRIPS & MUSHROOMS (ID: 8047a201-d61b-4bf7-b742-ff7519469bd7)
--   Price: 0, Cost: 188025.0, Type: 1, Sales Dept: 5
--   Ingredients (12 items):
---- UNSALTED BUTTER: 15.00 G = 345.0
---- UHT MILK: 130.00 G = 390.0
---- MARINATED CHICKEN FILLET (WIP): 100.00 G = 100000.0
---- MUSHROOM: 50.00 G = 83350.0
---- VEGETABLE OIL: 10.00 ML = 32.0
---- SPRING ONIONS: 0.00 G = 0.0
---- PARMESSAN CHEESE: 5.00 G = 0.0
---- VANILLA EXTRACT: 2.00 G = 160.0
---- EGG: 1.00 PCS = 648.0
---- BECHAMEL SAUCE (WIP): 1.00 PORTION = 1000.0
---- ... and 2 more ingredients

-- Product: RED VELVET MARBLE (ID: 3c866239-d0e2-4917-904b-a0052ed1157e)
--   Price: 0, Cost: 0.0, Type: 2, Sales Dept: 5
--   Ingredients (1 items):
---- CREAM CHEESE ICING (WIP): 4.00 G = 0.0

-- Product: ORANGE JUICE (ID: 3acb54e8-382f-4189-a799-5ccad27fda71)
--   Price: 0, Cost: 700.0, Type: 4, Sales Dept: 5
--   Ingredients (2 items):
---- ICE: 20.00 G = 0.0
---- ORANGE: 4.00 PCS = 700.0

-- Product: PINEAPPLE ZINGER SMOTHIE (ID: 3a8c423c-5123-4238-a1b5-80f973d10a37)
--   Price: 0, Cost: 1850566.0, Type: 4, Sales Dept: 5
--   Ingredients (5 items):
---- PINEAPPLE FRUIT: 100.00 G = 566.0
---- C-WAY WATER: 100.00 G = 150000.0
---- PURE HONEY: 50.00 G = 460000.0
---- CUCUMBER: 120.00 G = 840000.0
---- GINGER: 50.00 G = 400000.0

-- Product: PINEAPPLE JUICE (ID: 1d8ecfae-03c3-4835-b104-8b7c1fe0cde3)
--   Price: 0, Cost: 283.0, Type: 4, Sales Dept: 5
--   Ingredients (2 items):
---- PINEAPPLE FRUIT: 50.00 G = 283.0
---- ICE: 10.00 G = 0.0

-- Product: FRENCH TOAST WITH ASSORTED FRUITS (ID: 4ed59b55-7cc4-47ba-a739-ea71bbacdf5d)
--   Price: 0, Cost: 150000.0, Type: 1, Sales Dept: 5
--   Ingredients (3 items):
---- FRENCH TOAST (WIP): 1.00 PCS = 0.0
---- ASSORTED FRUIT (WIP): 80.00 G = 80000.0
---- MARPLE SYRUP (WIP): 70.00 G = 70000.0

-- Product: UHT MILK (CORNERSTORE) (ID: 7a782d18-6b1d-49ee-b59f-b4c89e9770d8)
--   Price: 0, Cost: 3000.0, Type: 4, Sales Dept: 5
--   Ingredients (1 items):
---- UHT MILK: 1,000.00 G = 3000.0

-- Product: WAFFLES WITH ASSORTED FRUIT (ID: ed8ac92c-4c76-4779-9aa1-8dc88aa83263)
--   Price: 0, Cost: 555100.0, Type: 1, Sales Dept: 5
--   Ingredients (4 items):
---- WAFFLES BATTER: 345.00 G = 345000.0
---- APPLE FRUIT: 20.00 G = 100.0
---- RED GRAPE: 20.00 G = 140000.0
---- MARPLE SYRUP (WIP): 70.00 G = 70000.0

-- Product: MINI CROISSANT (ID: bd493087-eed3-4c18-b5e4-c18430d71788)
--   Price: 0, Cost: 0.0, Type: 2, Sales Dept: 5
--   Ingredients (1 items):
---- MINI CROISSANT DOUGH (WIP): 12.00 PCS = 0.0

-- Product: CHOCOLATE CHIP COOKIES (ID: a92b8147-79bd-4df1-9e11-6d2bc004f68a)
--   Price: 0, Cost: 2974.75, Type: 2, Sales Dept: 5
--   Ingredients (10 items):
---- WHITE FLOUR: 103.33 G = 113.663
---- GRANULATED SUGAR: 50.00 G = 84.0
---- UNSALTED BUTTER: 75.00 G = 1725.0
---- EGG: 0.00 PCS = 0.0
---- SALT: 2.00 G = 1.0
---- BAKING SODA: 1.67 G = 0.0
---- VANILLA EXTRACT: 1.67 G = 133.6
---- BROWN SUGAR: 55.00 G = 165.0
---- BAKING POWDER: 0.67 G = 2.5125
---- DARK CHOCOLATE CHIP: 83.33 G = 749.97

-- Product: TRIO CLASSIC CAKE (ID: 398fe671-9e48-4f55-ae75-6fefb95a3f6c)
--   Price: 0, Cost: 257.16, Type: 2, Sales Dept: 5
--   Ingredients (6 items):
---- CREAM CHEESE ICING (WIP): 107.14 G = 0.0
---- BUTTER CREAM (WIP): 107.14 G = 0.0
---- 10 INCHES VANILLA SPONGE CAKE(WIP): 0.00 PCS = 0.0
---- 10 INCHES CHOCOLATE SPONGE CAKE(WIP0: 0.00 PCS = 0.0
---- 10 INCHES RED VELVET CAKE(WIP): 0.00 PCS = 0.0
---- DARK CHOCOLATE BLOCK: 21.43 G = 257.16

-- Product: RED VELVET SLICE CAKE (ID: 5abd4372-98df-4d46-bbf7-f0a022d547d0)
--   Price: 0, Cost: 3087.08, Type: 2, Sales Dept: 5
--   Ingredients (11 items):
---- GRANULATED SUGAR: 0.00 G = 0.0
---- RED SUGAR FLAIR: 0.00 G = 0.0
---- COCOA POWDER: 0.00 G = 0.0
---- SALT: 0.00 G = 0.0
---- BAKING SODA: 0.00 G = 0.0
---- WHITE VINEGAR: 0.00 ML = 0.0
---- UHT MILK: 0.00 G = 0.0
---- WHITE FLOUR: 0.00 G = 0.0
---- VEGETABLE OIL: 0.00 ML = 0.0
---- CREAM CHEESE ICING (WIP): 0.00 G = 0.0
---- ... and 1 more ingredients

-- Product: WHITE FOREST SLICE (ID: 9f073233-2bbf-42da-b822-ede03c6530cc)
--   Price: 0, Cost: 1546.46, Type: 2, Sales Dept: 5
--   Ingredients (10 items):
---- WHITE FLOUR: 67.50 G = 74.25
---- GRANULATED SUGAR: 56.25 G = 94.5
---- SALT: 1.13 G = 0.565
---- VEGETABLE OIL: 16.88 ML = 54.016
---- UHT MILK: 22.50 G = 67.5
---- EGG: 0.00 PCS = 0.0
---- VANILLA EXTRACT: 1.88 G = 150.4
---- WHITE VINEGAR: 1.13 ML = 565.0
---- WHIPPING CREAM: 112.50 G = 0.0
---- FRUIT COCKTAIL: 78.75 G = 540.225

-- Product: WILDBERRIES SORBET (ID: 670a4247-b29e-453b-b000-8dee3da807fd)
--   Price: 0, Cost: 4501512.0, Type: 3, Sales Dept: 5
--   Ingredients (4 items):
---- GRANULATED SUGAR: 900.00 G = 1512.0
---- C-WAY WATER: 3,000.00 G = 4500000.0
---- SUPERGLEMIX: 150.00 G = 0.0
---- WILDBERRIES: 0.00 CU = 0.0

-- Product: WAFFLES WITH EGGS AND BACON (SCRAMBLED) (ID: 9a39b4c3-8a5d-4be8-b05c-c160b07132ce)
--   Price: 0, Cost: 417215.0, Type: 1, Sales Dept: 5
--   Ingredients (4 items):
---- WAFFLES BATTER: 345.00 G = 345000.0
---- BACON: 45.00 G = 1215.0
---- MARPLE SYRUP (WIP): 70.00 G = 70000.0
---- EGG SCRAMBLED (WIP): 1.00 PCS = 1000.0

-- Product: COOKIES & CREAM MILKSHAKE (ID: 05ba7f2d-7e8a-4387-a9fd-704defd52466)
--   Price: 0, Cost: 277.5, Type: 4, Sales Dept: 5
--   Ingredients (5 items):
---- HAZELNUT SPREAD: 30.00 G = 0.0
---- VANILLA  GELATO(F02): 110.00 G = 0.0
---- PEANUT TOPPING: 50.00 G = 0.0
---- UHT MILK: 30.00 G = 90.0
---- CONDENSED MILK: 50.00 ML = 187.5

-- Product: MATCHA LATTE N (ID: d4aa7746-774f-4d46-843f-247022c20ea5)
--   Price: 0, Cost: 8712.5, Type: 4, Sales Dept: 5
--   Ingredients (3 items):
---- MATCHA POWDER: 5.00 G = 0.0
---- UHT MILK: 200.00 G = 600.0
---- CONDENSED MILK: 30.00 ML = 112.5

-- Product: COLESLAW (SIDE ATTRACTIONS) (ID: ac33da1d-345f-4cb0-944a-ff7221affbc3)
--   Price: 0, Cost: 249671.5, Type: 1, Sales Dept: 5
--   Ingredients (4 items):
---- CABBAGE: 70.00 G = 171.5
---- CARROT: 5.00 G = 7500.0
---- CUCUMBER: 30.00 G = 210000.0
---- COLESLAW CREAM (WIP): 32.00 G = 32000.0

-- Product: CLASSIC WAFFLES (ID: 1297a0c7-8996-44ff-acaa-0881daadee1d)
--   Price: 0, Cost: 415000.0, Type: 1, Sales Dept: 5
--   Ingredients (2 items):
---- WAFFLES BATTER: 345.00 G = 345000.0
---- MARPLE SYRUP (WIP): 70.00 G = 70000.0

-- Product: PANCAKE BATTER (ID: 2aaf54ac-eb71-42cd-9621-6152d12ee88d)
--   Price: 0, Cost: 861.72, Type: 1, Sales Dept: 5
--   Ingredients (6 items):
---- UNSALTED BUTTER: 25.00 G = 575.0
---- UHT MILK: 40.00 G = 120.0
---- VANILLA EXTRACT: 2.00 G = 160.0
---- ICING SUGAR: 3.00 G = 6.72
---- PANCAKE MIX (WIP): 182.00 G = 0.0
---- egg production: 1.00 G = 0.0

-- Product: FRAPUCCINO COFFEE (STRAWBERRY) (ID: e892e4c1-4311-44cb-8ed6-86f078448805)
--   Price: 0, Cost: 4357952.5, Type: 4, Sales Dept: 5
--   Ingredients (8 items):
---- COFFEE BEAN CAFFEINATED: 18.00 G = 0.0
---- DRY SUGAR: 50.00 G = 3925000.0
---- CONDENSED MILK: 30.00 ML = 112.5
---- UHT MILK: 80.00 G = 240.0
---- STRAWBERRY: 60.00 G = 390000.0
---- C-WAY WATER: 20.00 G = 30000.0
---- POWDERED MILK: 60.00 G = 12600.0
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 1.00 RO = 0.0

-- Product: WAFFLE CONE (ID: e4dbeaf5-744e-46ee-8941-92f9e2521644)
--   Price: 0, Cost: 33207.0, Type: 2, Sales Dept: 5
--   Ingredients (3 items):
---- WHITE FLOUR: 750.00 G = 825.0
---- GRANULATED SUGAR: 525.00 G = 882.0
---- POWDERED MILK: 150.00 G = 31500.0

-- Product: GINGER SNAP COOKIES GELATO (ID: ed687402-011f-477b-8809-5502c1aa83d3)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (3 items):
---- BASE ALBA: 3,000.00 G = 120000.0
---- SPEKULOOS: 240.00 G = 0.0
---- QUELLA CRUNCHY: 0.00 G = 0.0

-- Product: PEANUT BUTTER GELATO (ID: 62fc86af-c1d1-4da6-8882-e993f19a0969)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (3 items):
---- BASE ALBA: 3,000.00 G = 120000.0
---- QUELLA PEANUT: 100.00 G = 0.0
---- PASTA SCROK: 240.00 CU = 0.0

-- Product: HAZELNUT GELATO (ID: 76591ba3-114e-4fbe-9459-2c9903b4cec2)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (3 items):
---- NOCCIOLA SELECTION PASTA: 0.00 G = 0.0
---- BASE ALBA: 3,000.00 G = 120000.0
---- STAB HAZELNUT (E35): 240.00 G = 0.0

-- Product: BISCOTTO GELATO (ID: d008908e-e2bb-4e30-ad88-fb3e70be3d3e)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (3 items):
---- BASE ALBA: 3,000.00 G = 120000.0
---- VARIEGATO CHOCOBISCOTTO: 0.00 G = 0.0
---- PASTA BISCOTTINO: 150.00 G = 0.0

-- Product: CARAMEL GELATO (ID: cc8670ce-b0b9-427a-8e88-7ef1ee1d1fe3)
--   Price: 0, Cost: 720000.0, Type: 3, Sales Dept: 5
--   Ingredients (3 items):
---- BASE ALBA: 3,000.00 G = 120000.0
---- PASTA CARAMEL: 120.00 G = 0.0
---- SALTY CARAMEL CREME: 100.00 G = 600000.0

-- Product: VANILLA GELATO (ID: 6bfa20f6-eef2-42c1-bbab-fbcd81fd1e81)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (2 items):
---- BASE ALBA: 3,000.00 G = 120000.0
---- VANILLA  50 (FLAVOURING PASTE) - E40: 90.00 G = 0.0

-- Product: RUBY CHEESE CAKE GELATO (ID: 3f0559f7-34d5-42af-bdef-74aea7591c0a)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (3 items):
---- QUELLA RUBY: 90.00 G = 0.0
---- CHEESE CAKE BASE: 120.00 G = 0.0
---- BASE ALBA: 3,000.00 G = 120000.0

-- Product: REDVELVET GELATO (ID: 064ea612-4cde-4c9b-bdf3-c54de7c662f3)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (2 items):
---- BASE ALBA: 3,000.00 G = 120000.0
---- REDVELVET CRUMBS: 0.00 G = 0.0

-- Product: COOKIES AND CREAM GELATO (ID: 65c92579-01c8-4823-9b08-174e301c62c7)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (2 items):
---- BASE ALBA: 3,000.00 G = 120000.0
---- COOKIES THE ORIGINAL: 200.00 G = 0.0

-- Product: CARAMEL CHEESE CAKE (ID: a69b2e04-aeab-4163-83df-36cd26217823)
--   Price: 0, Cost: 2189599.19, Type: 2, Sales Dept: 5
--   Ingredients (6 items):
---- CREAM CHEESE: 133.33 G = 0.0
---- GRANULATED SUGAR: 53.33 G = 89.5944
---- CARAMEL SAUCE: 66.67 ML = 0.0
---- WHIPPING CREAM: 226.67 G = 0.0
---- GELATINE LEAF: 12.80 G = 2146176.0
---- DIGESTIVE BISCUIT: 80.00 G = 43333.6

-- Product: CARAMEL SAUCE (ID: da02d584-af91-4f48-a7f6-97265fb09582)
--   Price: 0, Cost: 1825.08, Type: 2, Sales Dept: 5
--   Ingredients (4 items):
---- GRANULATED SUGAR: 515.63 G = 866.2584
---- WHIPPING CREAM: 309.38 G = 0.0
---- UNSALTED BUTTER: 30.94 G = 711.62
---- VANILLA EXTRACT: 3.09 G = 247.2

-- Product: PRAWN PENNE PESTO (ID: 811f6203-0b05-4db6-8e1c-13b1003638f3)
--   Price: 0, Cost: 558605.0, Type: 1, Sales Dept: 5
--   Ingredients (10 items):
---- PENNE PASTA: 110.00 G = 554400.0
---- SEASONED PRWANS (WIP): 1.00 PORTION = 0.0
---- PESTO SAUCE: 100.00 G = 0.0
---- SALT: 10.00 G = 5.0
---- GARLIC: 10.00 G = 0.0
---- OLIVE OIL: 20.00 ML = 0.0
---- BLACK PEPPER POWDER: 1.00 G = 4200.0
---- PARMESSAN CHEESE: 20.00 G = 0.0
---- MARKET CHILLI PEPPER: 5.00 G = 0.0
---- HAZELNUT SPREAD: 20.00 G = 0.0

-- Product: A PIECE OF NAPLES (ID: 2df06560-7f01-444c-aac1-a2e90bdfde7b)
--   Price: 0, Cost: 32.0, Type: 1, Sales Dept: 5
--   Ingredients (4 items):
---- Skillet frittata (WIP): 1.00 PORTION = 0.0
---- SAUSAGE: 2.00 PCS = 0.0
---- Texas toast(WIP): 1.00 PORTION = 0.0
---- VEGETABLE OIL: 10.00 ML = 32.0

-- Product: MACCHIATO (ID: ad1bc08f-f885-4d1b-a050-99243830ee03)
--   Price: 0, Cost: 1200.0, Type: 4, Sales Dept: 5
--   Ingredients (3 items):
---- COFFEE BEAN CAFFEINATED: 36.00 G = 0.0
---- UHT MILK: 400.00 G = 1200.0
---- CORNERSTORE BRANDED PAPER  COFFEE CUP: 2.00 RO = 0.0

-- Product: STRAWBERRY MUM (ID: e1a104cb-aeb0-4c49-9428-2fb8dde5538a)
--   Price: 0, Cost: 1375850.0, Type: 4, Sales Dept: 5
--   Ingredients (6 items):
---- ORANGE: 2.00 PCS = 350.0
---- LEMON: 60.00 G = 90000.0
---- ORANGE JUICE (WIP): 60.00 ML = 888000.0
---- C-WAY WATER: 200.00 G = 300000.0
---- STRAWBERRY: 15.00 G = 97500.0
---- FRAGOLA  (STRAWBERRY ) TOPPING (I07): 60.00 G = 0.0

-- Product: DO IT FOR THE BRITS (SUNNY SIDE UP) (ID: 1db57dd3-3aa9-4543-845d-f4849b4ce277)
--   Price: 0, Cost: 219787.0, Type: 1, Sales Dept: 5
--   Ingredients (9 items):
---- SAUSAGE: 2.00 PCS = 0.0
---- BAKED BEANS IN TOMATO SAUCE: 100.00 G = 0.0
---- MUSHROOM: 50.00 G = 83350.0
---- UNSALTED BUTTER: 30.00 G = 690.0
---- FRESH TOMATOES: 50.00 G = 125000.0
---- BACON: 45.00 G = 1215.0
---- VEGETABLE OIL: 10.00 ML = 32.0
---- SUNNY SIDE UP: 1.00 PORTION = 6500.0
---- LOAF BREAD (WIP): 3.00 PCS = 3000.0

-- Product: WAFFLE MAGIC (ID: 79f6804a-0714-425c-bfe3-6ffd681d0c6a)
--   Price: 0, Cost: 1000.0, Type: 1, Sales Dept: 5
--   Ingredients (2 items):
---- WAFFLES BATTER: 1.00 G = 1000.0
---- VANILLA ICE CREAM: 1.00 CU = 0.0

-- Product: PANCAKES WITH ASSORTED FRUITS (ID: 4a1be0ad-22c1-419f-9646-f9a5b5cf73c7)
--   Price: 0, Cost: 52000.0, Type: 1, Sales Dept: 5
--   Ingredients (3 items):
---- PANCAKE BATTER: 2.00 PORTION = 2000.0
---- ASSORTED FRUIT (WIP): 26.67 G = 26670.0
---- MARPLE SYRUP (WIP): 23.33 G = 23330.0

-- Product: SCREW DRIVER (ID: e18163e2-2bf7-4bcc-af86-e25d3600a6f4)
--   Price: 0, Cost: 3530500.0, Type: 4, Sales Dept: 5
--   Ingredients (4 items):
---- VODKA: 50.00 ML = 650000.0
---- ORANGE JUICE (WIP): 60.00 ML = 888000.0
---- DRY SUGAR: 25.00 G = 1962500.0
---- C-WAY WATER: 20.00 G = 30000.0

-- Product: CLASSIC BACON (ID: 5eb9b859-5d59-44d2-97b7-5a04164fdf14)
--   Price: 0, Cost: 3390.0, Type: 1, Sales Dept: 5
--   Ingredients (2 items):
---- BACON: 90.00 G = 2430.0
---- VEGETABLE OIL: 300.00 ML = 960.0

-- Product: CLASSIC FRENCH TOAST (ID: a19bfd75-c084-4cdf-b52b-36d23913b745)
--   Price: 0, Cost: 70000.0, Type: 1, Sales Dept: 5
--   Ingredients (2 items):
---- FRENCH TOAST (WIP): 1.00 PCS = 0.0
---- MARPLE SYRUP (WIP): 70.00 G = 70000.0

-- Product: MANGO GELATO (ID: c2d1a826-7b31-49f6-b223-cf3f6671ba2c)
--   Price: 0, Cost: 1512.0, Type: 3, Sales Dept: 5
--   Ingredients (4 items):
---- GRANULATED SUGAR: 900.00 G = 1512.0
---- PASTA MANGO ALPHONSO: 0.00 G = 0.0
---- SUPERGLEMIX: 150.00 G = 0.0
---- BOREHOLE WATER: 3,000.00 G = 0.0

-- Product: PINEAPPLE JUICE STORE (ID: 83447213-9a57-452b-8999-cde9e62d66ef)
--   Price: 0, Cost: 141.5, Type: 4, Sales Dept: 5
--   Ingredients (2 items):
---- PINEAPPLE FRUIT: 25.00 G = 141.5
---- ICE: 5.00 G = 0.0

-- Product: TOAST BREAD (ID: 95698317-fce1-4be9-984e-d21ca76186d1)
--   Price: 0, Cost: 0.0, Type: 1, Sales Dept: 5
--   Ingredients (1 items):
---- TOAST BREAD: 3.00 PCS = 0.0

-- Product: SEX IN THE DRIVEWAY (ID: 0ea05210-f6f9-46a1-85ad-4524960194ac)
--   Price: 0, Cost: 1378340.0, Type: 4, Sales Dept: 5
--   Ingredients (4 items):
---- PEACH SCHNAPPS: 50.00 ML = 0.0
---- VODKA: 100.00 ML = 1300000.0
---- BLUE CURACOA: 100.00 ML = 0.0
---- PLASTIC SPRITE: 200.00 ML = 78340.0

-- Product: BASE ALBA (ID: 00155933-0488-467b-81ee-4d1b9bc7fa2a)
--   Price: 0, Cost: 1082312.96, Type: 3, Sales Dept: 5
--   Ingredients (4 items):
---- POWDERED MILK: 3,952.00 G = 829920.0
---- BASE ALBA: 6,080.00 G = 243200.0
---- BOREHOLE WATER: 26,440.00 G = 0.0
---- GRANULATED SUGAR: 5,472.00 G = 9192.96

-- Product: CHEESY BEEF PESTO MELT (ID: 7120f0b5-b0ac-446f-a854-5f1e0b43b451)
--   Price: 0, Cost: 3114654.0, Type: 1, Sales Dept: 5
--   Ingredients (10 items):
---- SEASONED BEEF: 100.00 G = 100000.0
---- PESTO SAUCE: 40.00 G = 0.0
---- CUCUMBER: 10.00 G = 70000.0
---- FRESH TOMATOES: 40.00 G = 100000.0
---- MOZZARELLA CHEESE: 50.00 G = 2759900.0
---- LETTUCE: 10.00 G = 80000.0
---- UNSALTED BUTTER: 30.00 G = 690.0
---- ONIONS: 20.00 G = 0.0
---- VEGETABLE OIL: 20.00 ML = 64.0
---- LOAF BREAD (WIP): 4.00 PCS = 4000.0

-- Product: DOLCE LATTE GELATO (ID: 9fdc2a1e-76e4-4dee-95ac-a2e74c7c2033)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (3 items):
---- VARIEGATO DOLCE DI LATTE: 150.00 G = 0.0
---- BASE ALBA: 3,000.00 G = 120000.0
---- DOLCE DE LATTE: 200.00 G = 0.0

-- Product: CREAM BRULEE GELATO (ID: 46d8edab-b53c-4807-bf87-ba09a342d95d)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (3 items):
---- BASE ALBA: 3,000.00 G = 120000.0
---- CREME BRULLE PASTA: 150.00 G = 0.0
---- VARIEGATO PER CREME BRULEE: 100.00 G = 0.0

-- Product: CHOCO COCONUT GELATO (ID: 037a1a51-89ba-462e-9cb4-2ac0adb31ebc)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (3 items):
---- BASE ALBA: 3,000.00 G = 120000.0
---- VARIEGATO MECRAPH: 66.00 CU = 0.0
---- MECRAPH GELATO: 240.00 CU = 0.0

-- Product: WHITE CHOCOLATE GELATO (ID: e1dca909-c1cc-4275-987c-4bdf2af9af81)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (3 items):
---- WHITE CHOCOLATE PASTA (E59): 240.00 G = 0.0
---- VARIEGATO CHOCOBISCOTTO: 0.00 G = 0.0
---- BASE ALBA: 3,000.00 G = 120000.0

-- Product: AMARENA CHERRY GELATO (ID: 49a31df0-57d5-4093-8c3f-c04c14f819d2)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (2 items):
---- BASE ALBA: 3,000.00 G = 120000.0
---- VARIEGATO AMARENA GRIOTTE: 100.00 G = 0.0

-- Product: STRACCIATELLA GELATO (ID: e24a1e17-caad-4696-86b0-55d438b54278)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (2 items):
---- COPERTURA STRACCIATELA: 100.00 G = 0.0
---- BASE ALBA: 3,000.00 G = 120000.0

-- Product: MACARONS (ID: 081f3f63-8b0e-4af0-811a-7da5db73eb42)
--   Price: 0, Cost: 0.0, Type: 2, Sales Dept: 5
--   Ingredients (1 items):
---- MACARON PRODUCTION: 21.00 PCS = 0.0

-- Product: BROWNIES GELATO (ID: 57ad66fc-7da9-4e73-86fb-631d324d4976)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (4 items):
---- BASE ALBA: 3,000.00 G = 120000.0
---- BROWNIES CRUMB: 0.00 G = 0.0
---- VAMOLA VARIEGATO: 200.00 G = 0.0
---- SMACK FLAVOURING (E36): 240.00 G = 0.0

-- Product: COFFEE GELATO (ID: 332360c4-ac72-47b4-b444-ab5161a60f09)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (2 items):
---- BASE ALBA: 3,000.00 G = 120000.0
---- PASTA CAFFEE CONCENTRATA: 60.00 G = 0.0

-- Product: COOKIES LEMON GELATO (ID: 1857ec16-ed21-4832-ac76-01b19b150b77)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (2 items):
---- BASE ALBA: 3,000.00 G = 120000.0
---- COOKIES LEMON: 200.00 G = 0.0

-- Product: PANNA COTTA GELATO (ID: 9d971725-2241-41a8-b725-61f8802f9554)
--   Price: 0, Cost: 120000.0, Type: 3, Sales Dept: 5
--   Ingredients (3 items):
---- BASE ALBA: 3,000.00 G = 120000.0
---- PASTA PANNA COTTA: 240.00 G = 0.0
---- FIORDILAMPONE: 100.00 G = 0.0

-- Product: MARINATED CHICKEN WINGS (ID: 5c1c9b15-6828-41f1-8af4-a3fe6aa5a802)
--   Price: 0, Cost: 131303979.68, Type: 1, Sales Dept: 5
--   Ingredients (14 items):
---- CHICKEN WING: 2,430.00 G = 131220000.0
---- BLACK PEPPER: 6.08 G = 0.0
---- Hot Chilli sauce (3.8kg): 12.15 G = 0.0
---- BLENDED PEPPER: 121.50 G = 0.0
---- DARK SOY SAUCE: 6.08 G = 0.0
---- KNOR MAGGI SEASONING (CHICKEN&BEEF): 12.15 G = 23085.0
---- SALT: 6.08 G = 3.04
---- OLIVE OIL: 36.45 ML = 0.0
---- VEGETABLE OIL: 36.45 ML = 116.64
---- WHITE VINEGAR: 6.08 ML = 3040.0
---- ... and 4 more ingredients

-- Product: MIDNIGHT CHOCOLATE GELATO (ID: 613ed6ba-932c-4023-801a-9347880d247b)
--   Price: 0, Cost: 36.4, Type: 3, Sales Dept: 5
--   Ingredients (3 items):
---- BASE ALBA: 0.91 G = 36.4
---- SMACK FLAVOURING (E36): 0.07 G = 0.0
---- CIOCCOMANIA: 0.07 G = 0.0

-- Product: VANILLA MILKSHAKE TAKE OUT (ID: 02285146-31cb-4100-bbe4-3b4ba12c6b21)
--   Price: 0, Cost: 6000.0, Type: 4, Sales Dept: 5
--   Ingredients (1 items):
---- VANILLA MILKSHAKE: 2.00 PCS = 0.0

-- Product: OMELETTE CHASSEUR (ID: 27b8014a-d5db-443a-8760-44c27b849bf5)
--   Price: 0, Cost: 538208.0, Type: 1, Sales Dept: 5
--   Ingredients (7 items):
---- EGG: 6.00 PCS = 3888.0
---- UNSALTED BUTTER: 40.00 G = 920.0
---- MUSHROOM: 200.00 G = 333400.0
---- SAUSAGE: 4.00 PCS = 0.0
---- PARMESSAN CHEESE: 20.00 G = 0.0
---- ASSORTED FRUIT (WIP): 200.00 G = 200000.0
---- PLAIN CROISSANT: 2.00 PCS = 0.0

-- Product: CORNERSTORE'S FAMOUS AVO TOAST (ID: a031077f-3de7-4a48-8803-0fea0469bfc8)
--   Price: 0, Cost: 510181.0, Type: 1, Sales Dept: 5
--   Ingredients (8 items):
---- PESTO SAUCE: 60.00 G = 0.0
---- CREAM CHEESE: 40.00 G = 0.0
---- AVOCADO PEAR: 1.00 PCS = 0.0
---- TOAST BREAD: 1.00 PCS = 0.0
---- BELL PEPPER: 20.00 G = 180.0
---- ONIONS: 15.00 G = 0.0
---- SALT: 2.00 G = 1.0
---- IRISH POTATO: 300.00 G = 510000.0

-- Product: BAILEYS SHOT (ID: a01cf9b8-ddc6-44a6-8027-750895245da8)
--   Price: 0, Cost: 1145000.0, Type: 4, Sales Dept: 5
--   Ingredients (2 items):
---- BAILEYS: 50.00 ML = 1100000.0
---- LEMON: 30.00 G = 45000.0

-- Product: WAFFLES WITH EGGS AND BACON (SUNNYSIDE UP) (ID: 4a422a5b-2b21-48eb-9015-44252652e13a)
--   Price: 0, Cost: 417215.0, Type: 1, Sales Dept: 5
--   Ingredients (4 items):
---- EGG SUNNY SIDE UP (WIP): 1.00 PCS = 1000.0
---- WAFFLES BATTER: 345.00 G = 345000.0
---- BACON: 45.00 G = 1215.0
---- MARPLE SYRUP (WIP): 70.00 G = 70000.0

-- Product: WAFFLE WITH EGGS AND BACONS (ID: bc4343c2-a3f6-4895-a133-7424a2c542b8)
--   Price: 0, Cost: 72217.24, Type: 1, Sales Dept: 5
--   Ingredients (4 items):
---- WAFFLES BATTER: 1.00 G = 1000.0
---- BACON: 45.00 G = 1215.0
---- MARPLE SYRUP (WIP): 70.00 G = 70000.0
---- ICING SUGAR: 1.00 G = 2.24

-- Product: Dry gin shot (ID: 66e8cb01-b6c2-4683-ba2e-6b95187e2ea2)
--   Price: 0, Cost: 645000.0, Type: 4, Sales Dept: 5
--   Ingredients (2 items):
---- GORDONS GIN: 50.00 ML = 600000.0
---- LEMON: 30.00 G = 45000.0

-- Product: CHAPMAN (ID: fe1e1718-e6b1-460c-9792-062baed0d882)
--   Price: 0, Cost: 1604585.0, Type: 4, Sales Dept: 5
--   Ingredients (7 items):
---- PLASTIC FANTA: 50.00 ML = 0.0
---- PLASTIC SPRITE: 50.00 ML = 19585.0
---- PLASTIC TEEM SODA: 150.00 ML = 840000.0
---- GRENADINE: 50.00 ML = 600000.0
---- MIX TO DRINK BLACK CURRANT: 50.00 G = 0.0
---- LEMON: 50.00 G = 75000.0
---- CUCUMBER: 10.00 G = 70000.0

-- Product: CORNERSTORE'S SMASHED CROISSANT BURGER (ID: ada1eb08-5e1c-4800-ab48-9ec4a9ce7a84)
--   Price: 0, Cost: 130460.0, Type: 1, Sales Dept: 5
--   Ingredients (6 items):
---- PLAIN CROISSANT: 1.00 PCS = 0.0
---- PLAIN CHEDDAR CHEESE: 2.00 G = 0.0
---- BEEF PATTIES (WIP): 100.00 G = 100000.0
---- FRENCH FRIES: 200.00 G = 0.0
---- UNSALTED BUTTER: 20.00 G = 460.0
---- KETCHUP & MAYO (WIP): 30.00 G = 30000.0

-- Product: CORNERSTORE'S FISH KATSU BURGER (ID: 3617a5ff-d947-4005-841e-4a01a29129e1)
--   Price: 0, Cost: 882953.0, Type: 1, Sales Dept: 5
--   Ingredients (10 items):
---- BURGER BREAD (WIP): 1.00 PCS = 1000.0
---- WHITE FISH FILLET: 250.00 G = 0.0
---- EGG: 1.00 PCS = 648.0
---- PANKO BREAD CRUMBS: 50.00 G = 0.0
---- TARTAR SAUCE: 50.00 G = 0.0
---- TOMATO KETCHUP: 60.00 G = 810000.0
---- PLAIN CHEDDAR CHEESE: 1.00 G = 0.0
---- COLESLAW (WIP): 70.00 G = 70000.0
---- UNSALTED BUTTER: 15.00 G = 345.0
---- VEGETABLE OIL: 300.00 ML = 960.0

-- Product: CORNERSTORE'S FESTIVE PARTY BOARD (ID: 7a71523a-6db2-4a4a-a0f2-c9174297005f)
--   Price: 0, Cost: 4000.0, Type: 1, Sales Dept: 5
--   Ingredients (8 items):
---- MINI MARGHERITA PIZZA: 2.00 PORTION = 0.0
---- CHICKEN KEEBAB (WIP): 2.00 PORTION = 2000.0
---- VEGETABLE SPRINGROLL (WIP): 2.00 PCS = 2000.0
---- SAMOSA: 2.00 PCS = 0.0
---- BBQ CHICKEN WING: 1.00 PCS = 0.0
---- SPICY WINGS: 1.00 PCS = 0.0
---- MINI BURGER: 2.00 PCS = 0.0
---- FRENCH FRIES: 260.00 G = 0.0

-- Product: FROZEN MARGARITA (ID: 3ee0e43a-7745-4caa-ac21-ad2a67679008)
--   Price: 0, Cost: 2070050.4, Type: 4, Sales Dept: 5
--   Ingredients (5 items):
---- C-WAY WATER: 20.00 G = 30000.0
---- LIME JUICE: 50.00 G = 0.0
---- TEQUILA: 60.00 ML = 1440000.0
---- TRIPLE SEC LIQUOR: 50.00 ML = 600000.0
---- GRANULATED SUGAR: 30.00 G = 50.4

-- Product: CHOCOLATE MOUSSE CAKE (ID: 1e9ddeea-6872-44ab-ad3f-a4efa3fefdb1)
--   Price: 0, Cost: 0.0, Type: 1, Sales Dept: 5
--   Ingredients (11 items):
---- WHITE FLOUR: 0.00 G = 0.0
---- GRANULATED SUGAR: 0.00 G = 0.0
---- BAKING SODA: 0.00 G = 0.0
---- SALT: 0.00 G = 0.0
---- EGG: 0.00 PCS = 0.0
---- UHT MILK: 0.00 G = 0.0
---- WHITE VINEGAR: 0.00 ML = 0.0
---- VANILLA EXTRACT: 0.00 G = 0.0
---- VEGETABLE OIL: 0.00 ML = 0.0
---- COCOA POWDER: 0.00 G = 0.0
---- ... and 1 more ingredients

-- Product: FRENCH TOAST WITH BUTTERMILK CHICKEN TENDERS (ID: 41cca151-3709-4a29-91e3-ee3c8863d72c)
--   Price: 0, Cost: 675000.0, Type: 1, Sales Dept: 5
--   Ingredients (4 items):
---- FRENCH TOAST (WIP): 1.00 PCS = 0.0
---- MARINATED CHICKEN FILLET (WIP): 150.00 G = 150000.0
---- CHICKEN TENDER MIX (WIP): 100.00 G = 0.0
---- MARPLE SYRUP: 70.00 G = 525000.0

-- Product: MAC & CHEESE WITH CRISPY CHICKEN (ID: d4ef4c68-17ed-4a56-a0b8-deef8f2e2407)
--   Price: 0, Cost: 8291285.9, Type: 1, Sales Dept: 5
--   Ingredients (19 items):
---- MACARONI: 80.00 G = 0.0
---- DANICA COOKING CREAM: 40.00 G = 6008424.4
---- UHT MILK: 150.00 G = 450.0
---- UNSALTED BUTTER: 15.00 G = 345.0
---- ONIONS: 10.00 G = 0.0
---- GARLIC: 5.00 G = 0.0
---- PEAK MILK: 20.00 ML = 20000.0
---- WHITE PEPPER POWDER: 1.00 G = 3500.0
---- BLACK PEPPER: 1.00 G = 0.0
---- CAJUN SPICE SEASONING: 2.00 G = 0.0
---- ... and 9 more ingredients

-- Product: CROISSANT EGGS BENEDICT (ID: eaacfcf0-24d6-4e39-ab1f-3a3bdcfcb035)
--   Price: 0, Cost: 512169.25, Type: 1, Sales Dept: 5
--   Ingredients (12 items):
---- PLAIN CROISSANT: 1.00 PCS = 0.0
---- POACHED EGG (WIP): 2.00 PCS = 0.0
---- BACON: 50.00 G = 1350.0
---- HOLLANDAISE SAUCE: 199.00 PORTION = 0.0
---- SPINACH: 60.00 G = 0.0
---- CAYENNE PEPPER: 0.10 G = 0.0
---- UNSALTED BUTTER: 25.00 G = 575.0
---- SALT: 0.50 G = 0.25
---- IRISH POTATO: 300.00 G = 510000.0
---- BELL PEPPER: 20.00 G = 180.0
---- ... and 2 more ingredients

-- Product: CORNERSTORE'S BRUNCH COMBO (ID: e6fe56d9-1a57-4d52-a034-abe030e0ea22)
--   Price: 0, Cost: 523159.0, Type: 1, Sales Dept: 5
--   Ingredients (9 items):
---- MARINATED CHICKEN WINGS: 300.00 G = 0.0
---- SWEET HOT SPICY SAUCE (WIP): 70.00 G = 70000.0
---- BACON: 45.00 G = 1215.0
---- SAUSAGE: 2.00 PCS = 0.0
---- WAFFLE MIX (WIP): 1.00 G = 0.0
---- BUTTERMILK PANCAKES: 3.00 PCS = 0.0
---- EGG: 3.00 PCS = 1944.0
---- MARPLE SYRUP: 60.00 G = 450000.0
---- MINI MAC & CHEESE: 1.00 G = 0.0

-- Product: PISTACHIO GELATO (ID: 8506ca83-83b5-4e58-91a5-656c4f1226ca)
--   Price: 0, Cost: 0.0, Type: 1, Sales Dept: 5
--   Ingredients (2 items):
---- BASE ALBA: 0.00 G = 0.0
---- PASTA PISTACHIO SELECTION: 0.00 G = 0.0

-- Product: CHICKEN ALFREDO PIZZA (ID: c9169f8c-5bee-4db8-a233-fba6e1d67d89)
--   Price: 0, Cost: 4636250.0, Type: 1, Sales Dept: 5
--   Ingredients (10 items):
---- WHITE SAUCE (WIP): 100.00 G = 100000.0
---- BELL PEPPER: 20.00 G = 180.0
---- UNSALTED BUTTER: 10.00 G = 230.0
---- ONIONS: 10.00 G = 0.0
---- GARLIC: 5.00 G = 0.0
---- MOZZARELLA CHEESE: 80.00 G = 4415840.0
---- MARINATED CHICKEN FILLET (WIP): 120.00 G = 120000.0
---- VEGETABLE OIL FOR FRYING: 20.00 G = 0.0
---- PARMESSAN CHEESE: 20.00 G = 0.0
---- PIZZA DOUGH 02 (WIP): 280.00 G = 0.0

-- Product: BUBBLE GUM GELATO (ID: 341486ba-4c57-435c-be50-91a85ea64078)
--   Price: 0, Cost: 100000000.0, Type: 3, Sales Dept: 5
--   Ingredients (2 items):
---- BASE ALBA: 2,500.00 G = 100000.0
---- BUBBLE GUM (E67): 0.00 G = 0.0

-- Product: CHOCOBISCOTTO GELATO (ID: 11421d88-11c9-40c1-bf42-7b0d68516592)
--   Price: 0, Cost: 120000000.0, Type: 3, Sales Dept: 5
--   Ingredients (3 items):
---- BASE ALBA: 3,000.00 G = 120000.0
---- CHOCOBISCOTTO (PASTA E74): 240.00 G = 0.0
---- VARIEGATO CHOCOBISCOTTO: 0.00 G = 0.0

-- Product: WAFERS GELATO (ID: a181812c-6da6-4d09-b97d-1f63d1809d8b)
--   Price: 0, Cost: 120000000.0, Type: 3, Sales Dept: 5
--   Ingredients (3 items):
---- PASTA WAFERS: 280.00 G = 0.0
---- VARIEGATO WAFERS: 100.00 G = 0.0
---- BASE ALBA: 3,000.00 G = 120000.0

-- Product: VANILLA GELATO CORNERSTORE (ID: 481c4f99-8962-46b2-bc2d-ce13d1834b7f)
--   Price: 0, Cost: 120000000.0, Type: 3, Sales Dept: 5
--   Ingredients (2 items):
---- BASE ALBA: 3,000.00 G = 120000.0
---- PASTA FRENCH VANILLA: 90.00 G = 0.0

-- Product: 6 INCHES REDVELVET BENTO CAKE (ID: e67c9c36-88dc-49b5-8d4f-d3eb32dda73f)
--   Price: 0, Cost: 0.0, Type: 1, Sales Dept: 5
--   Ingredients (11 items):
---- GRANULATED SUGAR: 0.00 G = 0.0
---- COCOA POWDER: 0.00 G = 0.0
---- SALT: 0.00 G = 0.0
---- BAKING SODA: 0.00 G = 0.0
---- EGG: 0.00 PCS = 0.0
---- WHITE VINEGAR: 0.00 ML = 0.0
---- UHT MILK: 0.00 G = 0.0
---- WHITE FLOUR: 0.00 G = 0.0
---- CREAM CHEESE ICING (WIP): 0.00 G = 0.0
---- BUTTER CREAM (WIP): 0.00 G = 0.0
---- ... and 1 more ingredients

-- Product: FRENCH TOAST WITH STEAK EGGS BENEDICT (ID: cdcfda75-f15a-49fc-9c38-94b11a3970cf)
--   Price: 0, Cost: 2437349.0, Type: 1, Sales Dept: 5
--   Ingredients (13 items):
---- FRENCH TOAST (WIP): 1.00 PCS = 0.0
---- BEEF FILLET: 200.00 G = 1400000.0
---- POACHED EGG (WIP): 2.00 PCS = 0.0
---- IRISH POTATO: 300.00 G = 510000.0
---- BELL PEPPER: 20.00 G = 180.0
---- ONIONS: 15.00 G = 0.0
---- VEGETABLE OIL: 40.00 ML = 128.0
---- BLACK PEPPER: 1.00 G = 0.0
---- OLIVE OIL: 20.00 ML = 0.0
---- SALT: 2.00 G = 1.0
---- ... and 3 more ingredients

-- Product: CORNERSTORE'S FESTIVE BRUNCH (ID: 5f69f315-e45d-44e2-9d55-d85f7a5d3b0a)
--   Price: 0, Cost: 1482904.5, Type: 1, Sales Dept: 5
--   Ingredients (12 items):
---- BEEF FILLET: 200.00 G = 1400000.0
---- BLACK PEPPER: 1.00 G = 0.0
---- SALT: 1.00 G = 0.5
---- OLIVE OIL: 40.00 ML = 0.0
---- FRENCH TOAST (WIP): 1.00 PCS = 0.0
---- ASSORTED FRUIT (WIP): 80.00 G = 80000.0
---- FRENCH FRIES: 260.00 G = 0.0
---- VEGETABLE OIL: 300.00 ML = 960.0
---- EGG: 3.00 PCS = 1944.0
---- MARPLE SYRUP: 0.00 G = 0.0
---- ... and 2 more ingredients

-- Product: CORNERSTORE'S BREAKFAST CROISSANT COMBO (ID: 6926b65a-7d9c-44b4-b9c0-e66385c75aba)
--   Price: 0, Cost: 512649.0, Type: 1, Sales Dept: 5
--   Ingredients (9 items):
---- PLAIN CROISSANT: 1.00 PCS = 0.0
---- EGG: 3.00 PCS = 1944.0
---- IRISH POTATO: 300.00 G = 510000.0
---- BELL PEPPER: 20.00 G = 180.0
---- AVOCADO PEAR: 1.00 PCS = 0.0
---- UNSALTED BUTTER: 20.00 G = 460.0
---- VEGETABLE OIL: 20.00 ML = 64.0
---- SALT: 2.00 G = 1.0
---- PARMESSAN CHEESE: 10.00 G = 0.0

-- Product: HONEY GLAZED BACON (ID: 6a814b12-f40d-47e3-a66d-1b6555aba98f)
--   Price: 0, Cost: 278462.0, Type: 1, Sales Dept: 5
--   Ingredients (3 items):
---- BACON: 90.00 G = 2430.0
---- PURE HONEY: 30.00 G = 276000.0
---- VEGETABLE OIL: 10.00 ML = 32.0

-- Product: CORNERSTORE'S NEW YEAR EV PASTA WITH CHICKEN (ID: 3732b180-cb32-428d-8a1c-db68fa81dbe5)
--   Price: 0, Cost: 1467695.0, Type: 1, Sales Dept: 5
--   Ingredients (14 items):
---- MARINATED CHICKEN FILLET (WIP): 150.00 G = 150000.0
---- PENNE PASTA: 110.00 G = 554400.0
---- TOMATO PASTE: 20.00 G = 160000.0
---- FRESH TOMATOES: 150.00 G = 375000.0
---- WHITE SAUCE (WIP): 200.00 G = 200000.0
---- ONIONS: 20.00 G = 0.0
---- GARLIC: 5.00 G = 0.0
---- RED CHILLI POWDER: 10.00 G = 24000.0
---- SALT: 2.00 G = 1.0
---- WHITE WINE: 20.00 ML = 0.0
---- ... and 4 more ingredients

-- Product: WAFFLES WITH EGGS AND BACON (OMELETTE) (ID: 635b28ed-57e7-4240-ba09-1437e7700ab2)
--   Price: 0, Cost: 416215.0, Type: 1, Sales Dept: 5
--   Ingredients (4 items):
---- WAFFLES BATTER: 345.00 G = 345000.0
---- EGG OMELETTE (WIP): 1.00 PORTION = 0.0
---- BACON: 45.00 G = 1215.0
---- MARPLE SYRUP (WIP): 70.00 G = 70000.0

-- Product: AFTER EIGHT (ID: a3e9a910-6d05-4905-aa82-9b941297fde4)
--   Price: 0, Cost: 120000000.0, Type: 4, Sales Dept: 5
--   Ingredients (3 items):
---- BASE ALBA: 3,000.00 G = 120000.0
---- PASTA MENTA: 150.00 G = 0.0
---- COPERTURA STRACCIATELA: 180.00 G = 0.0

-- Product: BLUEBERRY PANCAKES (ID: ff335b60-ce9d-4fa0-a74b-8de8e2e0465a)
--   Price: 0, Cost: 792000.0, Type: 1, Sales Dept: 5
--   Ingredients (3 items):
---- PANCAKE BATTER: 2.00 PORTION = 2000.0
---- BLUE BERRY: 100.00 G = 650000.0
---- MARPLE SYRUP (WIP): 140.00 G = 140000.0

-- Product: CORNERSTORE'S BUTTERMILK CHICKEN CROISSANT SANDO (ID: 64cebc56-d96d-470d-b23c-36277c4829b7)
--   Price: 0, Cost: 1130635.0, Type: 1, Sales Dept: 5
--   Ingredients (16 items):
---- PLAIN CROISSANT: 1.00 PCS = 0.0
---- CHICKEN TENDER MIX (WIP): 100.00 G = 0.0
---- MARINATED CHICKEN FILLET (WIP): 150.00 G = 150000.0
---- UHT MILK: 50.00 G = 150.0
---- WHITE VINEGAR: 10.00 ML = 5000.0
---- GREEK YOUGHURT: 20.00 G = 64.0
---- ONION POWDER: 2.00 G = 0.0
---- RED CHILLI POWDER: 2.00 G = 4800.0
---- GARLIC POWDER: 2.00 G = 5000.0
---- PAPRIKA: 2.00 G = 7000.0
---- ... and 6 more ingredients

-- Product: CORNERSTORE'S OREO PANCAKES (ID: fb6bb7e4-198b-4113-a5f4-5cbad34160d8)
--   Price: 0, Cost: 2060.0, Type: 1, Sales Dept: 5
--   Ingredients (5 items):
---- OREOS: 2.00 G = 1600.0
---- CHOCOLATE GLAZE: 100.00 G = 0.0
---- UNSALTED BUTTER: 20.00 G = 460.0
---- OREO POWDER: 20.00 G = 0.0
---- PANCAKES: 4.00 PCS = 0.0

-- Product: CORNERSTORE BBQ BEEF  PIZZA (ID: d9cb8eed-cee5-4301-88fd-d52e826d4789)
--   Price: 0, Cost: 4416020.0, Type: 1, Sales Dept: 5
--   Ingredients (8 items):
---- BBQ SAUCE: 120.00 G = 0.0
---- MOZZARELLA CHEESE: 80.00 G = 4415840.0
---- SEASONED BEEF FILLET: 200.00 G = 0.0
---- PARMESSAN CHEESE: 20.00 G = 0.0
---- PIZZA DOUGH 02 (WIP): 280.00 G = 0.0
---- BELL PEPPER: 20.00 G = 180.0
---- VEGETABLE OIL FOR FRYING: 10.00 G = 0.0
---- ONION CARAMELIZED: 40.00 G = 0.0

-- Product: COTTON CANDY (ID: c76ffbf8-7233-472b-82fe-300f7d9ec38b)
--   Price: 0, Cost: 0.0, Type: 1, Sales Dept: 5
--   Ingredients (1 items):
---- FLOSSY SUGAR (CANDY COTTON): 60.00 SPN = 0.0

-- Product: VALENTINE VANILLA SPONGE (ID: bdd9ea9d-3f53-464d-8670-3f34b8facc45)
--   Price: 0, Cost: 1340.34, Type: 2, Sales Dept: 5
--   Ingredients (9 items):
---- WHITE FLOUR: 46.88 G = 51.568
---- GRANULATED SUGAR: 42.19 G = 70.8792
---- UHT MILK: 15.00 G = 45.0
---- SALT: 0.75 G = 0.375
---- WHITE VINEGAR: 1.13 ML = 565.0
---- VANILLA EXTRACT: 0.75 G = 60.0
---- VEGETABLE OIL: 9.38 ML = 30.016
---- UNSALTED BUTTER: 22.50 G = 517.5
---- BUTTER CREAM (WIP): 34.38 G = 0.0

-- Product: VALENTINE CHOCOLATE SPONGE (ID: d598e0db-5e52-4cf4-980f-04432e46f1fa)
--   Price: 0, Cost: 11557.15, Type: 2, Sales Dept: 5
--   Ingredients (10 items):
---- WHITE FLOUR: 335.00 G = 368.5
---- GRANULATED SUGAR: 60.00 G = 100.8
---- SALT: 4.50 G = 2.25
---- WHITE VINEGAR: 9.00 ML = 4500.0
---- VANILLA EXTRACT: 9.00 G = 720.0
---- VEGETABLE OIL: 268.00 ML = 857.6
---- BUTTER CREAM (WIP): 156.50 G = 0.0
---- BAKING SODA: 9.00 G = 0.0
---- COCOA POWDER: 156.50 G = 5008.0
---- BUTTER MILK (WIP): 500.00 G = 0.0


-- ============================================================================
-- SUMMARY
-- ============================================================================
-- Total products (excluding WIPs): 299
-- Products from XLSX: 147
-- Products with recipes: 238
-- Unique ingredients across all products: 347

-- ============================================================================
-- Product Type Mapping:
--   product_type_id = 1: Hot Kitchen Items (produced by HOT KITCHEN)
--   product_type_id = 2: Pastry Items (produced by PASTRY)  
--   product_type_id = 3: Gelato Items (produced by GELATO)
--   product_type_id = 4: Cornerstone Items (produced by CORNERSTONE)
--
-- Sales Department Mapping (where products are sold):
--   sales_department_id = 5: Till Sales
--   sales_department_id = 6: Concession
--   sales_department_id = 7: Corner Store

-- ============================================================================
-- Ingredients (Items) - These should match with items table
-- ============================================================================
-- The ingredients from JSON should be matched with items table (raw materials)
-- Example: GRANULATED SUGAR, UNSALTED BUTTER, WHITE FLOUR, etc.
-- ============================================================================
