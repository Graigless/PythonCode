SELECT
	lg.customer_id,
	c.customer_name,
	lg.system_name ,
	lg.cluster_name,
	lg.expire_on_date,
	lg.bw_version,
	lg.generated_on_date,
	lg.reason,
	lg.lg_comments_warnings ,
	c.c_account_owner ,
	c.c_account_owner_email
FROM
	lrs.licenses_generated lg,
	lrs.customers c
WHERE
	expire_on_date between DATE_SUB(CURDATE(), INTERVAL 3 DAY) AND DATE_ADD(CURDATE(), INTERVAL 21 DAY)
	AND lg.customer_id = c.customer_id
ORDER BY
	expire_on_date DESC
