SELECT A.customer_id, D.customer_name, A.opportunity_name, DATE_FORMAT(A.opportunity_import_timestamp,'%m/%d/%Y') DATE, E.sku_name, C.bought_quantity
FROM lrs.opportunities A, lrs.sku_opportunities_associations B, lrs.sku_associations C, lrs.customers D, lrs.sku_list E
WHERE DATE(A.opportunity_import_timestamp) > DATE('2019-01-01')
AND A.customer_id = D.customer_id
AND E.sku_uid = C.sku_uid
AND A.lrs_status = "loaded"
AND A.opportunity_uid = B.opportunity_uid
AND B.sku_association_uid = C.sku_association_uid
AND C.sku_uid IN ( 4056, 4237)
Order by A.opportunity_import_timestamp;