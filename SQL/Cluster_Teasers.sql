SELECT 
	sl.product_code, 
	sl.sku_name, 
	sp.sku_profile_name,
	sta.quantity
FROM
	sku_teaser_assignment sta
LEFT JOIN lrs.sku_profiles sp ON
	sta.sku_profile_uid = sp.sku_profile_uid
LEFT JOIN lrs.sku_list sl ON
	sp.sku_uid = sl.sku_uid
WHERE
	as_cluster_uid = 7783
/* Where is the Ignore value stored?*/