SELECT
	sor.TYPE, 
	sl.product_code, 
	sl.sku_name, 
	sp.sku_profile_name ,
	le.licensable_entity_render_name, 
	sor.operation,
	sor.pack_name, 
	sor.value, 
	sor.description,  
	sorr.sku_overprovisiong_rule_reason_name , 
	sorr.sku_overprovisiong_rule_reason_description
FROM
	lrs.sku_overprovisiong_rules sor
LEFT JOIN lrs.sku_profiles sp ON
	sor.sku_profile_uid = sp.sku_profile_uid
LEFT JOIN lrs.sku_overprovisiong_rule_reasons sorr ON
	sor.sku_overprovisiong_rule_reason_uid = sorr.sku_overprovisiong_rule_reason_uid
LEFT JOIN lrs.sku_list sl ON
	sp.sku_uid = sl.sku_uid
LEFT JOIN lrs.licensable_entities le ON
	sor.licensable_entity_uid = le.licensable_entity_uid
WHERE
	sor.as_cluster_uid = 17369