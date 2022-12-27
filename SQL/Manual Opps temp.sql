SELECT A.customer_id, C.customer_name, A.opportunity_name, LEFT(A.opportunity_import_timestamp,10) AS date, B.manual_opportunity_rule_reason_name
FROM lrs.opportunities A, lrs.manual_opportunity_rule_reasons B, lrs.customers C
WHERE opportunity_name Like "%temp%"
AND A.manual_opportunity_rule_reason_uid = B.manual_opportunity_rule_reason_uid
AND A.customer_id = C.customer_id
AND manually_entered = 1
AND A.lrs_status = "loaded"