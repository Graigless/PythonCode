SELECT
	CAST(sl.product_code AS CHAR),
	sl.sku_name,
	sp.sku_profile_name,
	sla.quantity,
	le.licensable_entity_uid,
	le.licensable_entity_name ,
	le.licensable_entity_type,
	(spcri.relative_quantity / sl.base_unit) Amount,
	spcri.applicable_release,
	spcri.pack_name,
	sla.resolution_order
FROM
	lrs.sku_license_assignment sla ,
	lrs.sku_profiles sp,
	lrs.sku_list sl,
	lrs.sku_profile_content spc,
	lrs.sku_content sc,
	lrs.licensable_entities le,
	lrs.sku_profile_content_release_info spcri
WHERE
	sla.as_cluster_uid = 586
	AND sla.sku_profile_uid = sp.sku_profile_uid
	AND sl.sku_uid = sp.sku_uid
	AND sp.sku_profile_uid = spc.sku_profile_uid
	AND spc.sku_content_uid = sc.sku_content_uid
	AND spcri.sku_profile_content_uid = spc.sku_profile_content_uid
	AND sc.licensable_entity_uid = le.licensable_entity_uid
	AND spcri.applicable_release = "R24.0"
	AND spcri.include_in_license = 1
ORDER BY
	le.licensable_entity_uid,
	resolution_order