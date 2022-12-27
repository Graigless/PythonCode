SELECT
	c.customer_name ,
	o.opportunity_id,
	o.opportunity_name ,
	o.lrs_status ,
	o.description ,
	sl.product_code,
	sl.sku_name,
	sku_opportunity_qte
FROM
	opportunities o,
	lrs.sku_opportunities_associations soa,
	lrs.sku_associations sa,
	lrs.sku_list sl,
	lrs.customers c
WHERE
	o.opportunity_uid = soa.opportunity_uid
	AND soa.sku_association_uid = sa.sku_association_uid
	AND sl.sku_uid = sa.sku_uid
	AND c.customer_id = o.customer_id
	AND o.customer_id = "C10990"
	AND o.opportunity_uid = 20570