SELECT opportunity_uid, CONCAT('https://broadsoft.my.salesforce.com/', lrs.opportunities.opportunity_id) AS OpLink, customer_id, opportunity_name, order_status, lrs_status, reseller_id,	opportunity_import_timestamp, manually_entered, lrs.opportunities.*	
FROM lrs.opportunities
WHERE opportunity_import_timestamp > DATE_SUB(NOW(), INTERVAL 30 Day)
AND manually_entered = 0;