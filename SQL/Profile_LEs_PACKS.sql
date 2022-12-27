SELECT
	sl.sku_name,
	sp.sku_profile_name,
	le.licensable_entity_name ,
	le.licensable_entity_type,
	(spcri.relative_quantity / sl.base_unit) Amount,
	spcri.applicable_release,
	spcri.pack_name
FROM
	lrs.sku_list sl ,
	lrs.sku_profiles sp,
	lrs.sku_profile_content spc,
	lrs.sku_content sc,
	lrs.licensable_entities le,
	lrs.sku_profile_content_release_info spcri
WHERE
	sl.sku_uid = sp.sku_uid
	AND sp.sku_profile_uid = spc.sku_profile_uid
	AND spc.sku_content_uid = sc.sku_content_uid
	AND spcri.sku_profile_content_uid = spc.sku_profile_content_uid
	AND sc.licensable_entity_uid = le.licensable_entity_uid
	AND sp.sku_profile_uid = 4901
	AND spcri.applicable_release = "R24.0"
	AND spcri.include_in_license = 1
	ORDER BY spcri.pack_name, le.licensable_entity_name  ASC
