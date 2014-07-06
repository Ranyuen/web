<?php
$db = new PDO('sqlite:./db.sqlite3');
$sql = <<<SQL
INSERT INTO photo (id, description_ja, description_en, species_name, width, height)
VALUES
("1b647cd1-656d-4dcb-b513-a6e48a8a2885", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("1bb0031b-79a8-4c0e-b693-f2ee2aa0789a", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("3ac3afa0-ebac-4dd2-bcdf-b8d2ab651d89", :descriptionja, "Ponerorchis", "Ponerorchis", 1000, 1500),
("07f96dee-e558-46fb-b697-4026c74f5771", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("9e98baf1-c0dc-4142-a657-f0c37c9ac049", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("30cc89c5-173b-4c84-9ed8-9a054513399b", :descriptionja, "Ponerorchis", "Ponerorchis", 1000, 1500),
("68edbaf6-dbde-49a2-ab70-7ea1d9bbde55", :descriptionja, "Ponerorchis", "Ponerorchis", 1000, 1500),
("82c87643-f168-4e7c-ac35-35fe737f8d2c", :descriptionja, "Ponerorchis", "Ponerorchis", 1000, 1500),
("463da6b9-e522-4f69-9adf-db0ddb462941", :descriptionja, "Ponerorchis", "Ponerorchis", 1000, 1500),
("806e5791-9394-497e-89b7-9b68d6c897f6", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("3559f86e-5e1c-4b1a-b100-af4285a30c58", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("5506f34d-c608-4469-9408-8af39640f2a0", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("9572e7b4-9e48-4f14-ab43-8588334ef8a6", :descriptionja, "Ponerorchis", "Ponerorchis", 1000, 1500),
("419836bc-7056-4ffa-8c1c-34002d490a1f", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("9647479b-ff68-4316-9cfb-b3a76d28dfab", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("a409ccb0-283b-453b-b498-109f4b173308", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("ac54f5f1-24ac-4ddf-b52d-267c73c3e031", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("adfaec6f-49db-4e0f-9708-8869327eb4e5", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("b2a32197-6d3a-4799-8ca8-99fa5d6d7fb3", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("c701cc24-65b9-4b34-83b6-7caa88faed47", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("c8216ecc-a560-4d52-bac9-fd4c86dcef01", :descriptionja, "Ponerorchis", "Ponerorchis", 1000, 1500),
("cacd76e2-64fd-4bac-84ab-ecda2208bdc9", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("cbe62b59-4a3c-4f3b-8f69-c7f64d29582a", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("cf5ba7a9-52d3-46d4-8130-9b2fb2621813", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("e0da32f3-e644-4909-ba42-5292626c7026", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("e3086175-fdef-42ef-b738-66541a283f7c", :descriptionja, "Ponerorchis", "Ponerorchis", 1000, 1500),
("f72ca148-9792-4ad2-9fe4-7327d99d51b1", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000),
("fa2f22b7-2338-4e8f-acf2-6853ecff847c", :descriptionja, "Ponerorchis", "Ponerorchis", 1000, 1500),
("fadbda22-987d-40ae-a6bf-f0546aa9133d", :descriptionja, "Ponerorchis", "Ponerorchis", 1500, 1000);
SQL;
echo $sql;
$stmt = $db->prepare($sql);
var_dump($db->errorCode());
var_dump($db->errorInfo());
$stmt->execute(['descriptionja' => 'アワチドリ /　夢ちどり']);
var_dump($db->errorCode());
var_dump($db->errorInfo());
