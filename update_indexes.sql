-- Added by Hamza Rashid, dated: 2020-02-15

START TRANSACTION;
SET FOREIGN_KEY_CHECKS = 0;

DROP INDEX `idx_entityId` ON `account`;
CREATE INDEX `account_entityId_index` ON `account` (`entityId`);
ALTER TABLE `account` ADD CONSTRAINT `account_entityId_foreign` FOREIGN KEY (`entityId`) REFERENCES `entity`(`id`);
CREATE INDEX `account_statusId_index` ON `account` (`statusId`);

CREATE INDEX `apiRequestLog_userId_index` ON `apiRequestLog` (`userId`);
ALTER TABLE `apiRequestLog` ADD CONSTRAINT `apiRequestLog_userId_foreign` FOREIGN KEY (`userId`) REFERENCES `user`(`id`);
CREATE INDEX `apiRequestLog_type_index` ON `apiRequestLog` (`type`);

CREATE INDEX `bulkAddUpload_userId_index` ON `bulkAddUpload` (`userId`);
ALTER TABLE `bulkAddUpload` ADD CONSTRAINT `bulkAddUpload_userId_foreign` FOREIGN KEY (`userId`) REFERENCES `user`(`id`);
CREATE INDEX `bulkAddUpload_entityId_index` ON `bulkAddUpload` (`entityId`);
ALTER TABLE `bulkAddUpload` ADD CONSTRAINT `bulkAddUpload_entityId_foreign` FOREIGN KEY (`entityId`) REFERENCES `entity`(`id`);
CREATE INDEX `bulkAddUpload_statusId_index` ON `bulkAddUpload` (`statusId`);

DROP INDEX `idx_accountId` ON `cartDetail`;
CREATE INDEX `cartDetail_accountId_index` ON `cartDetail` (`accountId`);
ALTER TABLE `cartDetail` ADD CONSTRAINT `cartDetail_accountId_foreign` FOREIGN KEY (`accountId`) REFERENCES `account`(`id`);
CREATE INDEX `cartDetail_entityProductId_index` ON `cartDetail` (`entityProductId`);
-- TODO: add foreign key 'cartDetail_entityProductId_foreign'
CREATE INDEX `cartDetail_userId_index` ON `cartDetail` (`userId`);

CREATE INDEX `category_parent_id_index` ON `category` (`parent_id`);

CREATE INDEX `chatroom_sellerEntityId_index` ON `chatroom` (`sellerEntityId`);
ALTER TABLE `chatroom` ADD CONSTRAINT `chatroom_sellerEntityId_foreign` FOREIGN KEY (`sellerEntityId`) REFERENCES `entity`(`id`);
CREATE INDEX `chatroom_buyerEntityId_index` ON `chatroom` (`buyerEntityId`);
ALTER TABLE `chatroom` ADD CONSTRAINT `chatroom_buyerEntityId_foreign` FOREIGN KEY (`buyerEntityId`) REFERENCES `entity`(`id`);

CREATE INDEX `chatroomDetail_chatroomId_index` ON `chatroomDetail` (`chatroomId`);
ALTER TABLE `chatroomDetail` ADD CONSTRAINT `chatroomDetail_chatroomId_foreign` FOREIGN KEY (`chatroomId`) REFERENCES `chatroom`(`id`);
CREATE INDEX `chatroomDetail_senderUserId_index` ON `chatroomDetail` (`senderUserId`);
ALTER TABLE `chatroomDetail` ADD CONSTRAINT `chatroomDetail_senderUserId_foreign` FOREIGN KEY (`senderUserId`) REFERENCES `user`(`id`);
CREATE INDEX `chatroomDetail_senderEntityId_index` ON `chatroomDetail` (`senderEntityId`);
ALTER TABLE `chatroomDetail` ADD CONSTRAINT `chatroomDetail_senderEntityId_foreign` FOREIGN KEY (`senderEntityId`) REFERENCES `entity`(`id`);
CREATE INDEX `chatroomDetail_receiverEntityId_index` ON `chatroomDetail` (`receiverEntityId`);
ALTER TABLE `chatroomDetail` ADD CONSTRAINT `chatroomDetail_receiverEntityId_foreign` FOREIGN KEY (`receiverEntityId`) REFERENCES `entity`(`id`);
CREATE INDEX `chatroomDetail_type_index` ON `chatroomDetail` (`type`);
CREATE INDEX `chatroomDetail_isRead_index` ON `chatroomDetail` (`isRead`);

CREATE INDEX `city_countryId_index` ON `city` (`countryId`);
ALTER TABLE `city` ADD CONSTRAINT `city_countryId_foreign` FOREIGN KEY (`countryId`) REFERENCES `country`(`id`);

DROP INDEX `name_en_UNIQUE` ON `country`;
CREATE UNIQUE INDEX `country_name_en_unique` ON `country` (`name_en`);
-- TODO: add unique index 'country_name_ar_unique'
-- TODO: add unique index 'country_name_fr_unique'

CREATE INDEX `emailLog_type_index` ON `emailLog` (`type`);

ALTER TABLE `entity` DROP FOREIGN KEY `fkentitytypeId`;
DROP INDEX `fkentitytypeId_idx` ON `entity`;
CREATE INDEX `entity_entityTypeId_index` ON `entity` (`typeId`);
ALTER TABLE `entity` ADD CONSTRAINT `entity_entityType_foreign` FOREIGN KEY (`typeId`) REFERENCES `entityType`(`id`);
ALTER TABLE `entity` DROP FOREIGN KEY `fk_entity_country`;
ALTER TABLE `entity` ADD CONSTRAINT `entity_countryId_foreign` FOREIGN KEY (`countryId`) REFERENCES `country`(`id`);
CREATE INDEX `entity_currencyId_index` ON `currency` (`id`);
ALTER TABLE `entity` ADD CONSTRAINT `entity_currencyId_foreign` FOREIGN KEY (`currencyId`) REFERENCES `currency`(`id`);

CREATE INDEX `entityBranch_entityId_index` ON `entityBranch` (`entityId`);
ALTER TABLE `entityBranch` ADD CONSTRAINT `entityBranch_entityId_foreign` FOREIGN KEY (`entityId`) REFERENCES `entity`(`id`);
CREATE INDEX `entityBranch_cityId_index` ON `entityBranch` (`cityId`);
ALTER TABLE `entityBranch` ADD CONSTRAINT `entityBranch_cityId_foreign` FOREIGN KEY (`cityId`) REFERENCES `city`(`id`);

CREATE INDEX `entityChangeApproval_entityId_index` ON `entityChangeApproval` (`entityId`);
ALTER TABLE `entityChangeApproval` ADD CONSTRAINT `entityChangeApproval_entityId_foreign` FOREIGN KEY (`entityId`) REFERENCES `entity`(`id`);
CREATE INDEX `entityChangeApproval_userId_index` ON `entityChangeApproval` (`userId`);
ALTER TABLE `entityChangeApproval` ADD CONSTRAINT `entityChangeApproval_userId_foreign` FOREIGN KEY (`userId`) REFERENCES `user`(`id`);
CREATE INDEX `entityChangeApproval_isApproved_index` ON `entityChangeApproval` (`isApproved`);

CREATE INDEX `entityChangeApprovalField_entityChangeApprovalId_index` ON `entityChangeApprovalField` (`entityChangeApprovalId`);
ALTER TABLE `entityChangeApprovalField` ADD CONSTRAINT `entityChangeApprovalField_entityChangeApprovalId_foreign` FOREIGN KEY (`entityChangeApprovalId`) REFERENCES `entityChangeApproval`(`id`);

CREATE INDEX `entityDashboardBanner_entityId_index` ON `entityDashboardBanner` (`entityId`);
ALTER TABLE `entityDashboardBanner` ADD CONSTRAINT `entityDashboardBanner_entityId_foreign` FOREIGN KEY (`entityId`) REFERENCES `entity`(`id`);
CREATE INDEX `entityDashboardBanner_productId_index` ON `entityDashboardBanner` (`productId`);
ALTER TABLE `entityDashboardBanner` ADD CONSTRAINT `entityDashboardBanner_productId_foreign` FOREIGN KEY (`productId`) REFERENCES `product`(`id`);
CREATE INDEX `entityDashboardBanner_countryId_index` ON `entityDashboardBanner` (`countryId`);
ALTER TABLE `entityDashboardBanner` ADD CONSTRAINT `entityDashboardBanner_countryId_foreign` FOREIGN KEY (`countryId`) REFERENCES `country`(`id`);

CREATE INDEX `entityMinimumValueOrder_entityId_index` ON `entityMinimumValueOrder` (`entityId`);
ALTER TABLE `entityMinimumValueOrder` ADD CONSTRAINT `entityMinimumValueOrder_entityId_foreign` FOREIGN KEY (`entityId`) REFERENCES `entity`(`id`);

CREATE INDEX `entityMinimumValueOrderCity_entityMinimumValueOrderId_index` ON `entityMinimumValueOrderCity` (`entityMinimumValueOrderId`);
ALTER TABLE `entityMinimumValueOrderCity` ADD CONSTRAINT `entityMinimumValueOrderCity_entityMinimumValueOrderId_foreign` FOREIGN KEY (`entityMinimumValueOrderId`) REFERENCES `entityMinimumValueOrder`(`id`);
CREATE INDEX `entityMinimumValueOrderCity_cityId_index` ON `entityMinimumValueOrderCity` (`cityId`);
ALTER TABLE `entityMinimumValueOrderCity` ADD CONSTRAINT `entityMinimumValueOrderCity_cityId_foreign` FOREIGN KEY (`cityId`) REFERENCES `city`(`id`);

CREATE INDEX `entityPaymentMethod_entityId_index` ON `entityPaymentMethod` (`entityId`);
ALTER TABLE `entityPaymentMethod` ADD CONSTRAINT `entityPaymentMethod_entityId_foreign` FOREIGN KEY (`entityId`) REFERENCES `entity`(`id`);
CREATE INDEX `entityPaymentMethod_paymentMethodId_index` ON `entityPaymentMethod` (`paymentMethodId`);
ALTER TABLE `entityPaymentMethod` ADD CONSTRAINT `entityPaymentMethod_paymentMethodId_foreign` FOREIGN KEY (`paymentMethodId`) REFERENCES `paymentMethod`(`id`);

ALTER TABLE `entityProductSell` DROP FOREIGN KEY `fk_entityProductSell_entityProductSellBonusDetail`;
ALTER TABLE `entityProductSell` DROP FOREIGN KEY `fk_entityProductSell_entity`;
ALTER TABLE `entityProductSell` DROP FOREIGN KEY `fk_entityProductSell_product`;
ALTER TABLE `entityProductSell` DROP FOREIGN KEY `fk_entityProductSell_stockStatus`;
ALTER TABLE `entityProductSell` DROP FOREIGN KEY `fk_entityProductSell_entityProductBonusType`;
ALTER TABLE `entityProductSell` DROP FOREIGN KEY `fk_entityProductSell_entityRelationGroup`;
ALTER TABLE `entityProductSell` ADD CONSTRAINT `entityProductSell_id_foreign` FOREIGN KEY (`id`) REFERENCES `entityProductSellBonusDetail`(`entityProductId`);
ALTER TABLE `entityProductSell` ADD CONSTRAINT `entityProductSell_entityId_foreign` FOREIGN KEY (`entityId`) REFERENCES `entity`(`id`);
ALTER TABLE `entityProductSell` ADD CONSTRAINT `entityProductSell_productId_foreign` FOREIGN KEY (`productId`) REFERENCES `product`(`id`);
ALTER TABLE `entityProductSell` ADD CONSTRAINT `entityProductSell_stockStatusId_foreign` FOREIGN KEY (`stockStatusId`) REFERENCES `stockStatus`(`id`);
ALTER TABLE `entityProductSell` ADD CONSTRAINT `entityProductSell_bonusTypeId_foreign` FOREIGN KEY (`bonusTypeId`) REFERENCES `bonusType`(`id`);
ALTER TABLE `entityProductSell` ADD CONSTRAINT `entityProductSell_bonusCustomerGroupId_foreign` FOREIGN KEY (`bonusCustomerGroupId`) REFERENCES `entityRelationGroup`(`id`);

DROP INDEX `idx_entityProductId` ON `entityProductSellBonusDetail`;
-- TODO: add foreign key 'entityProductSellBonusDetail_entityProductId_foreign'
CREATE INDEX `entityProductSellBonusDetail_bonusTypeId_index` ON `entityProductSellBonusDetail`(`bonusTypeId`);
ALTER TABLE `entityProductSellBonusDetail` ADD CONSTRAINT `entityProductSellBonusDetail_bonusTypeId_foreign` FOREIGN KEY (`bonusTypeId`) REFERENCES `bonusType`(`id`);

CREATE INDEX `entityProductSellBonusDetailRelationGroup_bonusId_index` ON `entityProductSellBonusDetailRelationGroup`(`bonusId`);
-- TODO: add foreign key 'entityProductSellBonusDetailRelationGroup_bonusId_foreign'
CREATE INDEX `entityProductSellBonusDetailRelationGroup_relationGroupId_index` ON `entityProductSellBonusDetailRelationGroup`(`relationGroupId`);
-- TODO: add foreign key 'entityProductSellBonusDetailRelationGroup_relationGroupId_foreign'

CREATE INDEX `entityProductSellEvent_entityProductId_index` ON `entityProductSellEvent`(`entityProductId`);
-- TODO: add foreign key 'entityProductSellEvent_entityProductId_foreign'
CREATE INDEX `entityProductSellEvent_userId_index` ON `entityProductSellEvent`(`userId`);
ALTER TABLE `entityProductSellEvent` ADD CONSTRAINT `entityProductSellEvent_userId_foreign` FOREIGN KEY (`userId`) REFERENCES `user`(`id`);

CREATE INDEX `entityRelation_entityBuyerId_index` ON `entityRelation`(`entityBuyerId`);
ALTER TABLE `entityRelation` ADD CONSTRAINT `entityRelation_entityBuyerId_foreign` FOREIGN KEY (`entityBuyerId`) REFERENCES `entity`(`id`);
CREATE INDEX `entityRelation_entitySellerId_index` ON `entityRelation`(`entitySellerId`);
ALTER TABLE `entityRelation` ADD CONSTRAINT `entityRelation_entitySellerId_foreign` FOREIGN KEY (`entitySellerId`) REFERENCES `entity`(`id`);
CREATE INDEX `entityRelation_relationGroupId_index` ON `entityRelation`(`relationGroupId`);
-- TODO: add foreign key 'entityRelation_relationGroupId_foreign'
CREATE INDEX `entityRelation_currencyId_index` ON `entityRelation`(`currencyId`);
ALTER TABLE `entityRelation` ADD CONSTRAINT `entityRelation_currencyId_foreign` FOREIGN KEY (`currencyId`) REFERENCES `currency`(`id`);
CREATE INDEX `entityRelation_statusId_index` ON `entityRelation`(`statusId`);

CREATE INDEX `entityRelationGroup_entityId_index` ON `entityRelationGroup`(`entityId`);
ALTER TABLE `entityRelationGroup` ADD CONSTRAINT `entityRelationGroup_entityId_foreign` FOREIGN KEY (`entityId`) REFERENCES `entity`(`id`);

CREATE INDEX `menuConfig_menuId_index` ON `menuConfig`(`menuId`);
ALTER TABLE `menuConfig` ADD CONSTRAINT `menuConfig_menuId_foreign` FOREIGN KEY (`menuId`) REFERENCES `menu`(`id`);
CREATE INDEX `menuConfig_entityTypeId_index` ON `menuConfig`(`entityTypeId`);
ALTER TABLE `menuConfig` ADD CONSTRAINT `menuConfig_entityTypeId_foreign` FOREIGN KEY (`entityTypeId`) REFERENCES `entityType`(`id`);
CREATE INDEX `menuConfig_userRoleId_index` ON `menuConfig`(`userRoleId`);
ALTER TABLE `menuConfig` ADD CONSTRAINT `menuConfig_userRoleId_foreign` FOREIGN KEY (`userRoleId`) REFERENCES `userRole`(`id`);

CREATE INDEX `menuItem_menuId_index` ON `menuItem`(`menuId`);
ALTER TABLE `menuItem` ADD CONSTRAINT `menuItem_menuId_foreign` FOREIGN KEY (`menuId`) REFERENCES `menu`(`id`);
CREATE INDEX `menuItem_parentId_index` ON `menuItem`(`parentId`);
CREATE INDEX `menuItem_order_index` ON `menuItem`(`order`);

CREATE INDEX `menuItemApp_menuId_index` ON `menuItemApp`(`menuId`);
ALTER TABLE `menuItemApp` ADD CONSTRAINT `menuItemApp_menuId_foreign` FOREIGN KEY (`menuId`) REFERENCES `menu`(`id`);
CREATE INDEX `menuItemApp_parentItemId_index` ON `menuItemApp`(`parentItemId`);
CREATE INDEX `menuItemApp_orderId_index` ON `menuItemApp`(`orderId`);
CREATE INDEX `menuItemApp_isActive_index` ON `menuItemApp`(`isActive`);

CREATE INDEX `news_typeId_index` ON `news`(`typeId`);
ALTER TABLE `news` ADD CONSTRAINT `news_typeId_foreign` FOREIGN KEY (`typeId`) REFERENCES `newsType`(`id`);

CREATE INDEX `order_entityBuyerId_index` ON `order`(`entityBuyerId`);
ALTER TABLE `order` ADD CONSTRAINT `order_entityBuyerId_foreign` FOREIGN KEY (`entityBuyerId`) REFERENCES `entity`(`id`);
CREATE INDEX `order_entitySellerId_index` ON `order`(`entitySellerId`);
ALTER TABLE `order` ADD CONSTRAINT `order_entitySellerId_foreign` FOREIGN KEY (`entitySellerId`) REFERENCES `entity`(`id`);
CREATE INDEX `order_branchBuyerId_index` ON `order`(`branchBuyerId`);
ALTER TABLE `order` ADD CONSTRAINT `order_branchBuyerId_foreign` FOREIGN KEY (`branchBuyerId`) REFERENCES `entityBranch`(`id`);
CREATE INDEX `order_branchSellerId_index` ON `order`(`branchSellerId`);
ALTER TABLE `order` ADD CONSTRAINT `order_branchSellerId_foreign` FOREIGN KEY (`branchSellerId`) REFERENCES `entityBranch`(`id`);
CREATE INDEX `order_userBuyerId_index` ON `order`(`userBuyerId`);
ALTER TABLE `order` ADD CONSTRAINT `order_userBuyerId_foreign` FOREIGN KEY (`userBuyerId`) REFERENCES `user`(`id`);
CREATE INDEX `order_userSellerId_index` ON `order`(`userSellerId`);
ALTER TABLE `order` ADD CONSTRAINT `order_userSellerId_foreign` FOREIGN KEY (`userSellerId`) REFERENCES `user`(`id`);
CREATE INDEX `order_statusId_index` ON `order`(`statusId`);
CREATE INDEX `order_currencyId_index` ON `order`(`currencyId`);
ALTER TABLE `order` ADD CONSTRAINT `order_currencyId_foreign` FOREIGN KEY (`currencyId`) REFERENCES `currency`(`id`);
CREATE INDEX `order_orderGrandId_index` ON `order`(`orderGrandId`);
ALTER TABLE `order` ADD CONSTRAINT `order_orderGrandId_foreign` FOREIGN KEY (`orderGrandId`) REFERENCES `orderGrand`(`id`);
CREATE INDEX `order_paymentMethodId_index` ON `order`(`paymentMethodId`);
ALTER TABLE `order` ADD CONSTRAINT `order_paymentMethodId_foreign` FOREIGN KEY (`paymentMethodId`) REFERENCES `paymentMethod`(`id`);
DROP INDEX `serial_UNIQUE` ON `order`;
CREATE UNIQUE INDEX `order_serial_unique` ON `order`(`serial`);

CREATE INDEX `orderDetail_orderId_index` ON `orderDetail`(`orderId`);
ALTER TABLE `orderDetail` ADD CONSTRAINT `orderDetail_orderId_foreign` FOREIGN KEY (`orderId`) REFERENCES `order`(`id`);
CREATE INDEX `orderDetail_entityProductId_index` ON `orderDetail`(`entityProductId`);
-- TODO: add foreign key 'orderDetail_entityProductId_foreign'

CREATE INDEX `orderGrand_buyerEntityId_index` ON `orderGrand`(`buyerEntityId`);
ALTER TABLE `orderGrand` ADD CONSTRAINT `orderGrand_buyerEntityId_foreign` FOREIGN KEY (`buyerEntityId`) REFERENCES `entity`(`id`);
CREATE INDEX `orderGrand_buyerBranchId_index` ON `orderGrand`(`buyerBranchId`);
ALTER TABLE `orderGrand` ADD CONSTRAINT `orderGrand_buyerBranchId_foreign` FOREIGN KEY (`buyerBranchId`) REFERENCES `entityBranch`(`id`);
CREATE INDEX `orderGrand_buyerUserId_index` ON `orderGrand`(`buyerUserId`);
ALTER TABLE `orderGrand` ADD CONSTRAINT `orderGrand_buyerUserId_foreign` FOREIGN KEY (`buyerUserId`) REFERENCES `user`(`id`);

CREATE INDEX `orderLog_orderId_index` ON `orderLog`(`orderId`);
ALTER TABLE `orderLog` ADD CONSTRAINT `orderLog_orderId_foreign` FOREIGN KEY (`orderId`) REFERENCES `order`(`id`);
CREATE INDEX `orderLog_userId_index` ON `orderLog`(`userId`);
ALTER TABLE `orderLog` ADD CONSTRAINT `orderLog_userId_foreign` FOREIGN KEY (`userId`) REFERENCES `user`(`id`);
CREATE INDEX `orderLog_statusId_index` ON `order`(`statusId`);

CREATE INDEX `orderMissingProduct_orderId_index` ON `orderMissingProduct`(`orderId`);
ALTER TABLE `orderMissingProduct` ADD CONSTRAINT `orderMissingProduct_orderId_foreign` FOREIGN KEY (`orderId`) REFERENCES `order`(`id`);
CREATE INDEX `orderMissingProduct_statusId_index` ON `order`(`statusId`);
CREATE INDEX `orderMissingProduct_buyerUserId_index` ON `orderMissingProduct`(`buyerUserId`);
ALTER TABLE `orderMissingProduct` ADD CONSTRAINT `orderMissingProduct_buyerUserId_foreign` FOREIGN KEY (`buyerUserId`) REFERENCES `user`(`id`);
DROP INDEX `id_UNIQUE` ON `orderMissingProduct`;

CREATE INDEX `orderRating_orderId_index` ON `orderRating`(`orderId`);
ALTER TABLE `orderRating` ADD CONSTRAINT `orderRating_orderId_foreign` FOREIGN KEY (`orderId`) REFERENCES `order`(`id`);
CREATE INDEX `orderRating_userId_index` ON `orderRating`(`userId`);
ALTER TABLE `orderRating` ADD CONSTRAINT `orderRating_userId_foreign` FOREIGN KEY (`userId`) REFERENCES `user`(`id`);
CREATE INDEX `orderRating_rateId_index` ON `orderRating`(`rateId`);
ALTER TABLE `orderRating` ADD CONSTRAINT `orderRating_rateId_foreign` FOREIGN KEY (`rateId`) REFERENCES `rate`(`id`);

CREATE INDEX `product_scientificNameId_index` ON `product`(`scientificNameId`);
ALTER TABLE `product` ADD CONSTRAINT `product_scientificNameId_foreign` FOREIGN KEY (`scientificNameId`) REFERENCES `scientificName`(`id`);
ALTER TABLE `product` DROP FOREIGN KEY `fk_product_country`;
ALTER TABLE `product` DROP FOREIGN KEY `fk_product_category`;
ALTER TABLE `product` DROP FOREIGN KEY `fk_product_subcategory`;
ALTER TABLE `product` ADD CONSTRAINT `product_country_foreign` FOREIGN KEY (`madeInCountryId`) REFERENCES `country`(`id`);
ALTER TABLE `product` ADD CONSTRAINT `product_category_foreign` FOREIGN KEY (`categoryId`) REFERENCES `category`(`id`);
ALTER TABLE `product` ADD CONSTRAINT `product_subcategory_foreign` FOREIGN KEY (`subcategoryId`) REFERENCES `subcategory`(`id`);

CREATE INDEX `productIngredient_productId_index` ON `productIngredient`(`productId`);
ALTER TABLE `productIngredient` ADD CONSTRAINT `productIngredient_productId_foreign` FOREIGN KEY (`productId`) REFERENCES `product`(`id`);
CREATE INDEX `productIngredient_ingredientId_index` ON `productIngredient`(`ingredientId`);
ALTER TABLE `productIngredient` ADD CONSTRAINT `productIngredient_ingredientId_foreign` FOREIGN KEY (`ingredientId`) REFERENCES `ingredient`(`id`);

-- TODO: add primary key `id`
-- TODO: add index `productsPOC_productId_index`
-- TODO: add foreign key `productsPOC_productId_foreign`

CREATE INDEX `productSubimage_productId_index` ON `productSubimage`(`productId`);
ALTER TABLE `productSubimage` ADD CONSTRAINT `productSubimage_productId_foreign` FOREIGN KEY (`productId`) REFERENCES `product`(`id`);

DROP INDEX `name` ON `scientificName`;
DROP INDEX `name_UNIQUE` ON `scientificName`;
CREATE INDEX `scientificName_name_unique` ON `scientificName`(`name`);
CREATE FULLTEXT INDEX `scientificName_name_fulltext` ON `scientificName`(`name`);
ALTER TABLE `scientificName` ADD CONSTRAINT `scientificName_categoryId_foreign` FOREIGN KEY (`categoryId`) REFERENCES `category`(`id`);
ALTER TABLE `scientificName` ADD CONSTRAINT `scientificName_subCategoryId_foreign` FOREIGN KEY (`subCategoryId`) REFERENCES `subcategory`(`id`);

CREATE INDEX `stockUpdateUpload_userId_index` ON `stockUpdateUpload`(`userId`);
ALTER TABLE `stockUpdateUpload` ADD CONSTRAINT `stockUpdateUpload_userId_foreign` FOREIGN KEY (`userId`) REFERENCES `user`(`id`);
CREATE INDEX `stockUpdateUpload_entityId_index` ON `stockUpdateUpload`(`entityId`);
ALTER TABLE `stockUpdateUpload` ADD CONSTRAINT `stockUpdateUpload_entityId_foreign` FOREIGN KEY (`entityId`) REFERENCES `entity`(`id`);
CREATE INDEX `stockUpdateUpload_statusId_index` ON `stockUpdateUpload`(`statusId`);

CREATE INDEX `subcategory_categoryId_index` ON `subcategory`(`categoryId`);
ALTER TABLE `subcategory` ADD CONSTRAINT `subcategory_categoryId_foreign` FOREIGN KEY (`categoryId`) REFERENCES `category`(`id`);

CREATE INDEX `supportLog_entityId_index` ON `supportLog`(`entityId`);
ALTER TABLE `supportLog` ADD CONSTRAINT `supportLog_entityId_foreign` FOREIGN KEY (`entityId`) REFERENCES `entity`(`id`);
CREATE INDEX `supportLog_userId_index` ON `supportLog`(`userId`);
ALTER TABLE `supportLog` ADD CONSTRAINT `supportLog_userId_foreign` FOREIGN KEY (`userId`) REFERENCES `user`(`id`);
CREATE INDEX `supportLog_typeId_index` ON `supportLog`(`typeId`);
ALTER TABLE `supportLog` ADD CONSTRAINT `supportLog_typeId_foreign` FOREIGN KEY (`typeId`) REFERENCES `supportType`(`id`);
CREATE INDEX `supportLog_supportReasonId_index` ON `supportLog`(`supportReasonId`);
ALTER TABLE `supportLog` ADD CONSTRAINT `supportLog_supportReasonId_foreign` FOREIGN KEY (`supportReasonId`) REFERENCES `supportReason`(`id`);

CREATE INDEX `supportReason_isAuth_index` ON `supportReason`(`isAuth`);

DROP INDEX `uid_UNIQUE` ON `user`;
CREATE UNIQUE INDEX `user_uid_unique` ON `user`(`uid`);
DROP INDEX `email_UNIQUE` ON `user`;
CREATE UNIQUE INDEX `user_email_unique` ON `user`(`email`);
ALTER TABLE `user` DROP FOREIGN KEY `fkuserstatusid`;
DROP INDEX `fkuserstatusid_idx` ON `user`;
CREATE INDEX `user_statusId_index` ON `user`(`statusId`);
ALTER TABLE `user` ADD CONSTRAINT `user_statusId_foreign` FOREIGN KEY (`statusId`) REFERENCES `userStatus`(`id`);
ALTER TABLE `user` DROP FOREIGN KEY `fkuserroleid`;
DROP INDEX `fkuserroleid_idx` ON `user`;
CREATE INDEX `user_roleId_index` ON `user`(`roleId`);
ALTER TABLE `user` ADD CONSTRAINT `user_roleId_foreign` FOREIGN KEY (`roleId`) REFERENCES `userRole`(`id`);

CREATE INDEX `userAccount_userId_index` ON `userAccount`(`userId`);
ALTER TABLE `userAccount` ADD CONSTRAINT `userAccount_userId_foreign` FOREIGN KEY (`userId`) REFERENCES `user`(`id`);
CREATE INDEX `userAccount_accountId_index` ON `userAccount`(`accountId`);
ALTER TABLE `userAccount` ADD CONSTRAINT `userAccount_accountId_foreign` FOREIGN KEY (`accountId`) REFERENCES `user`(`id`);
CREATE INDEX `userAccount_statusId_index` ON `user`(`statusId`);

CREATE INDEX `userResetToken_userId_index` ON `userResetToken`(`userId`);
ALTER TABLE `userResetToken` ADD CONSTRAINT `userResetToken_userId_foreign` FOREIGN KEY (`userId`) REFERENCES `user`(`id`);
CREATE INDEX `userResetToken_userResetTokenStatusId_index` ON `userResetToken`(`userResetTokenStatusId`);
ALTER TABLE `userResetToken` ADD CONSTRAINT `userResetToken_userResetTokenStatusId_foreign` FOREIGN KEY (`userResetTokenStatusId`) REFERENCES `userResetTokenStatus`(`id`);

CREATE INDEX `userRole_isSystem_index` ON `userRole`(`isSystem`);
CREATE INDEX `userRole_userId_index` ON `userRole`(`menuId`);
ALTER TABLE `userRole` ADD CONSTRAINT `userRole_menuId_foreign` FOREIGN KEY (`menuId`) REFERENCES `menu`(`id`);

CREATE INDEX `userSession_userId_index` ON `userSession`(`userId`);
ALTER TABLE `userSession` ADD CONSTRAINT `userSession_userId_foreign` FOREIGN KEY (`userId`) REFERENCES `user`(`id`);
CREATE INDEX `userSession_isActive_index` ON `userSession`(`isActive`);

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
