import pandas as pd

# Read File
customer_sample_file = pd.read_excel("CustomerSample.xlsx", sheet_name="Prospects", parse_dates=[0])

# Get records from 2017 or earlier
customers_2017_or_earlier = customer_sample_file[customer_sample_file["DateTime Recorded"] < "2018-01-01"]

# Output the records
customers_2017_or_earlier.to_excel("Customers2017OrEarlier.xlsx")