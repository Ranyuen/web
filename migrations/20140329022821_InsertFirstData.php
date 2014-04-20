<?php

use Phpmig\Migration\Migration;

class InsertFirstData extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "insert into photo (id, description_ja, description_en, species_name, width, height)
values
('fb71c5b3-2cd0-4d9c-b899-c49148dd5dff', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('fbd5f1c7-c56a-4e41-b0ea-4d5d6dd5254e', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('fbd9fad1-9ded-4d28-95c5-6d5f2e539d8e', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('fd00d25f-c34e-428d-bee2-ac08cd455e8e', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('fd811e59-b72c-4ea2-9a06-803ec3cc537f', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('fe01f83b-c138-4afc-9330-b691cd48e023', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('fe48d09d-f96d-4e64-9e41-6fce1b08607a', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('ffc971b6-c95e-41d5-9364-6c7f9da2e505', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('8748c133-28f8-4d97-babe-9f26c8fe55d9', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('880d35e3-1db3-4062-a551-4779e25840e9', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('88b30df5-c91e-4cfa-a31f-3c5fd0020b8a', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('8aa4c79c-2095-46fe-9068-db91203e6bcf', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('8af5f0ac-bfa0-4637-8502-99c983c3102c', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('8c7448be-3b2a-4c62-b590-36b4cdb7d546', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('8ce4c396-0d14-4b03-b5d3-fd73b196a07b', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('8dba2181-f6f9-4884-96cd-135c4eaa9d9b', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('9109ff0b-0fc0-4e9a-9155-674d708aa508', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('91dd627c-0e33-4df1-9766-14bd8906ed79', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('92d01b49-2fce-498f-af8a-2f6050e6e7bc', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('9374eefe-d77e-4fe1-acf0-b0c6fdfece09', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('93863a0d-15a4-4095-84b9-afcc178c36e0', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('95bf2c80-55c4-4412-86bf-0b38969f9bea', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('98281c2d-76bc-4f74-bec1-7cb74bd833f0', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('986b50c5-5b9c-4b6f-9128-4358f40616c4', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('98a457f9-ae76-4659-b79c-890348923e62', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('995a2291-b9cf-4521-8c23-68390fa90b5d', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('997a535e-ad1a-4574-940a-fc9a15ba075b', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('9a08eb7f-a1de-4d2e-8c46-a5670a93c390', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('9a98915c-a0dc-4c5c-a412-35b123976218', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('9aca0a38-af93-4382-bb6a-059be7473ea0', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('9c1b5251-2aa8-4756-b2af-68177d1a47ed', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('9d287851-3b2c-4333-95ad-441aaf0cbc5a', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('9e694708-aa02-4c91-b781-2a5547077fac', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('a153884c-85ce-465a-be31-c8dbd239139d', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('a1a0e196-0521-4b1f-bcc2-b502a8caec4b', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('a402c3ca-9375-4de0-b85a-b924c1d208d7', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('a4b8019b-a38a-4b19-a048-4d37179c956a', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('a52fdca5-e780-40d3-b94c-ae601637e467', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('a83d3e05-5206-44dc-bbb8-fcd15f24555f', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('a9e960ec-caae-4d66-8931-7f4d38335618', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('abb0b4be-1cd8-41c0-9fda-bbd23f4ed7ae', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('ac71071d-2a68-4ad1-bd1c-c4ac63d969ea', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('b4023f43-5ad9-4226-b322-0ce10d2388f0', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('b4481d23-5c8b-4075-9ee4-aaa5f33c253d', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('b6776a52-8a5c-454c-aca5-1e61c7a936b5', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('b7d7dfef-b970-4c69-b5b4-5a0279d4d6d2', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('b844d992-5d86-47c3-91ce-717b36ec9830', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('b8c6c70e-e9be-41f0-b9b5-19da74a514bc', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('b9bbbdd5-5d85-426e-8c83-55363219e1c9', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('ba68ec89-1353-4424-accb-9600180ba8d2', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('bbb3fab9-a21b-4379-9e2c-683a67fd9ff4', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('bbb810f3-1462-4199-a0ff-c690972bf988', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('bbe85be0-32fd-4320-ae37-ae1e80559a30', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('bcebe0fa-9b1b-4a0c-b033-45402d9395ef', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('be76ec40-5757-4fda-b830-88f959b6bab4', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('c2f8dc38-2172-4ab4-ba1d-429da420609c', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('c4f99d05-cd2e-4e24-8e4d-15dce1c93c3c', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('c6189c0f-2f35-41ea-86cc-6c96f5cb5d58', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('c6bc4c70-7ca1-4679-8ff4-5d47cb03084a', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('c7486725-7519-454b-ac91-d712c7fe9aec', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('c8f644be-307d-4000-a8c6-b86ea9c2c883', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('cb1a5a07-2c96-49b3-87ec-5b86ab76de6f', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('cb9228b3-508b-43c1-8e9f-e0e9239b6a84', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('cba50b6e-e712-4ddb-a2e7-eb6289693126', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('cd7eafca-d3e2-46e1-afe4-cac63126811c', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('cde37b2a-06b8-4efb-86fb-6a1aed2fafcc', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('ce1fdc0d-2de2-4ee4-ad45-4826dee7872d', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('ce957a34-c586-47b3-8094-f65c6b904cb6', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('cfc2e9bd-fa0f-43cd-b1f8-a510e3468ad4', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('d0e56f18-9095-4c09-8cab-bbda7810f644', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('d18031cd-719a-4723-8402-ed8700c9d898', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('d2106b66-e0fc-4c31-aacc-daf56394306e', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('d23c568f-4626-423a-adb9-341742fd6fcd', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('d310f8be-52c2-4aae-bf43-e8f8d16d906e', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('d33d65b3-44c4-4ab5-a46c-141bd38181fe', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('d38ac0a8-b79f-4e88-a2b4-f8f9013816b5', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('d3f84213-3f6b-4e2a-b360-52d787d6a960', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('d8b6e74f-31f4-46d0-8eb8-7a1d594f4fe6', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('d9ee93b7-c985-43a5-966e-51e04dcd1144', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('dab4efa2-11be-48f5-a55f-fff4d6605975', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('db789a48-a510-460e-aa0e-7026df5f0c58', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('dd3adaeb-e1a9-4f1b-b87c-e5f43abf873a', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('df53b08f-6dd0-45ee-b784-dde8c35cc851', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('e2e584fc-5ee7-4a6a-b762-ec9858df7a6a', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('e389353d-25dd-4d34-a1c3-86f43aaa89e3', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('e587c158-866c-43ae-a266-e2744b313ddc', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('e58d3d2a-3aed-4a22-8649-b08cf5a50d56', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('e5b88110-5434-4e5c-b1a6-9aba220823b2', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('e6544b18-9ae4-4d54-b2c2-c0b9f2e78d3d', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('e766a7d9-6687-434f-be90-030f9e7e3955', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('e825ea4a-c0cb-4a41-9c8d-32b8dcc2cdac', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('ea2726b5-97f5-46f4-be98-549473062527', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('ea38075e-8d80-415a-9b1d-2070865c2016', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('eaaba236-f3e2-4b7a-a162-d3dd6a2cb105', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('eca2a610-d73d-410a-aa81-1293bc33e961', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('edaa9ebc-353f-4807-8a99-67c879b1925e', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('ee9e8e76-81ab-4aea-8f97-adf156853845', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('ef2ab00f-ecff-4e47-b014-ac28a45ad0e6', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('efe93718-9f74-4067-b2cf-b358132f4461', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('f0baed78-140d-46d7-9f0d-02ff4d7d4347', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('f1a8b28b-68ea-4ba0-b155-1faf10e85d64', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('f2e94e26-2105-4217-8f69-89a9cd24745b', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('f2fd5f2e-0e0d-433d-9913-063771d459ec', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('f3445792-7d2f-4f18-b749-48b261b2c99b', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('f5e7de00-0e63-4dbe-9766-8bfacfa31ce7', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('f662f949-4b9f-40fa-b785-97fbfe5e136c', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('00269bbf-89a8-4e5f-abe7-c27c1d8ee4e9', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('00296843-524b-4342-ba98-892af24718ff', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('00765af0-7924-4d56-9af0-3e1f5b4df919', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('0499428a-0a0f-4fe6-9569-95e716e4cd56', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('09897dbf-fed7-41d1-99f9-07bd46ba2ad3', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('0a23140a-3b5b-46e4-bb0d-ca8618669fa3', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('0a7239af-eb1f-4014-b682-c323a243d215', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('0af748b0-e7a1-43b4-8b36-5ec4159c67bd', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('0f350d65-ef52-4caa-8b36-9f226e3b6ab1', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('0f4f057f-138c-4f4c-a949-4df1be5cb8aa', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('11ab0fbe-9b18-4e74-a9e9-bf4d3d907e14', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('1409a7eb-bbb3-4a86-bbee-d0702bdb9b76', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('15786ca7-3520-4574-86f7-d53cf989094e', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('1833132d-dfe9-4c42-aa2a-9eaa8c602735', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('18bca7ca-1a6a-43bb-b77d-2d550000c8ab', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('1aa49333-b7b4-4466-9457-3d7f17aaead2', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('1b48d870-6d58-4cd1-92cb-74613844ef3c', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('1b7bd4d3-074e-41ac-835e-7cc503db0c01', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('1ca75e88-c7ab-4f03-9849-78c21ee6217d', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('1df1b44b-9ab2-419d-be3c-569373246cbb', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('222ee621-dca4-42c7-93c6-a122c758aedd', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('22867d99-9abb-44a7-84d8-2f861a1edc7f', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('22e573d4-3672-4ed2-b838-82596b863457', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('24382571-d80c-415f-8685-bc34d6526d3d', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('25769326-8e61-40a6-9289-3910ea44c5a7', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('25937aec-f7a7-492f-9718-f5e134fe71b6', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('2650faf1-8b80-4910-bf2c-2a00c4863afe', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('267336c7-6a5d-43b1-9358-30e8eafc74cb', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('273da78c-92a9-4856-a8b7-ac73c8acd82a', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('28f68f85-df21-4597-9455-cf85595a6b9d', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('29d1ee3a-3184-4c69-b41c-559bce0e78f3', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('2b9cc63e-6ceb-499e-b105-b6491252f5ff', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('2f3bb2ad-debb-49ea-b613-5f8c431b22e3', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('30113e10-9feb-4473-a6cc-444f1f446053', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('30397de0-e287-4bbc-8447-2088097c3890', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('3078676d-17f5-4e31-a99d-ca2b60c18860', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('314b410c-cc43-41cb-abe7-d36f70498d04', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('32c9ee66-5455-4bd6-ae32-f4a01db1f57a', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('341b6439-2ee7-4076-9a64-1730a9645c4e', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('35d87d2f-f4ff-4a81-9c53-9a650f8bf12d', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('38574e98-1cc4-44cd-8fa9-94d70ec1af52', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('3a9b6713-9503-4f7c-a7cb-28973b2cc94e', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('3aca1902-41ca-474a-9852-40eea9de787c', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('3c5a70ef-a91e-4feb-96c5-5af8e69b1dac', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('3cc40e16-0d76-4989-b66b-a51ddf8e9927', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('3d4dbf0e-2798-494b-8651-f4c0d8391d5c', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('3d95780f-af39-468c-8b0c-2f5f79cd5b47', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('3e392dc7-52d1-4b64-ae30-69231ebe37bd', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('3eb381a6-18a7-4f14-afc3-675381b95f97', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('414ccd68-6169-4260-9a43-ad15666d4229', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('4163892e-e0e1-4ac6-9107-8a3a203616a7', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('423ef991-7441-47f8-b4a2-a957f6d3dbf2', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('4316d7f1-0a2b-4bdf-a69a-9474a1d0d38a', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('4377977c-0d1d-46da-bbca-84bd8e9904f5', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('43ca4a73-9600-4cab-81b8-34bfe29c48fa', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('43fff743-ccda-4e04-8b06-39614719851c', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('441c2cc8-9d34-43d4-b3d4-80f941974dab', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('45c32ba9-b533-4890-8c45-93daac20b163', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('469c0b4d-df51-42f3-b341-b47f47f35022', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('47f72e9e-6cf3-4f9f-aae7-cd8d299ec028', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('5058bc66-ac37-49bc-8c27-86cbef79491c', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('50c9f21a-9df9-4df9-a40e-22c49d8e27c1', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('51e84d86-0ac1-421f-85b5-cb435b47ad24', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('53e64524-01dd-4f2a-b476-788c2d7cdb97', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('56b7eccf-a95b-4459-99e7-2dd812e1b21a', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('58f449d4-d2ba-450b-8465-e71bab0ba018', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('597b204f-b42d-4438-bd4f-e1bda9f92804', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('5a94cbb2-c838-4eb3-87c0-0c9657e93e60', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('5ac7152d-7d0b-4339-82d8-9ef6b0663cc3', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('5c38f91b-d014-4ef5-bf1e-84cf2e3fd764', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('5dea7414-1929-456e-9be5-5f1eff67844b', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('6071337e-02d1-4fcb-9327-39c00b15982b', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('61916c3e-cdaf-4cf2-89b5-ccf0de4a09bc', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('619fbf59-24cf-4beb-a091-fb3c920a6e7b', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('61aa3354-6a0c-4a1c-9c54-47c5de34d314', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('6278ca3b-0f7d-4038-aff1-676cb0f7b5bf', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('65185537-3cab-478c-a766-9c35cd6df891', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('6774345d-20af-4206-8e04-445b77907bc2', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('67c6b2cb-b201-4e55-936d-86bde84c780e', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('6ac46cbb-71b6-4dcc-a399-a3542572ac43', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('6f7744aa-dfb1-4793-9f78-843e8c7a8c6d', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('71a9ec76-488c-4cbc-8811-cbcf88a7b1cb', 'エビネ', 'Calanthe', 'Calanthe', 1024, 1536),
('7232a2ce-e778-435f-a634-1afc5725ec6e', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('74625e7b-1e4d-489b-b741-0153398c9763', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('74836e8f-339a-4dfc-b3eb-0e64771e70c3', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('748a75af-1cc7-4883-915f-cddf5a877410', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('7bfb546b-e822-49fd-a02e-1aede490f817', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('7d714648-6383-4147-8e44-fd5dbc6fe1a2', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('7e0765e1-282d-4343-9d4a-12ef86d326d8', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('7edfdc2b-cb4f-43e4-acff-168d07ba3cb4', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('81eae02d-b4d7-4a88-a85c-661549b11ec7', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('826d0f30-7645-4b73-a1dd-7933c3a2dea0', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('835716c9-9a8d-4780-8ae0-36372c718cf3', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('842e79c1-ee90-4930-9440-9cd521c42b5a', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('868a7029-099b-4966-854d-f923abc11eab', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024),
('870ab613-ce48-4898-89b8-8baf9c3b04c1', 'エビネ', 'Calanthe', 'Calanthe', 1536, 1024)";
        $container = $this->getContainer();
        $container['db']->query($sql);
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $sql = 'delete from photo';
        $container = $this->getContainer();
        $container['db']->query($sql);
    }
}