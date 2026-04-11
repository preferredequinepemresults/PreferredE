import pandas as pd
from datetime import datetime

df = pd.read_csv("C://Users//monil//Download//Oct23_Excel.xls")

# Adding a new column SALEYEAR
saleyear = 2023
df['SALEYEAR'] = saleyear

# Adding a new column SALETYPE
saletype = 'Y'
df['SALETYPE'] = saletype

# Adding a new column SALECODE
salecode = ''
df['SALECODE'] = salecode

# Adding a new column SALEDATE
if 'SESSION' in df.columns:
    df['SALEDATE'] = df['SESSION']

# Adding a new column BOOK
book = 1
df['BOOK'] = book

# Initialize a counter
counter = 0

# Initialize a list to store the counter values
counter_values = []

# Iterate through the list of dates
for i, date_str in enumerate(df['SESSION']):
    # Convert the date string to a datetime object
    date = datetime.strptime(date_str, '%Y-%m-%d')
    
    # Check if this is the first date or if the date has changed from the previous one
    if i == 0 or date != prev_date:
        counter += 1  # Increment the counter when the date changes
        prev_date = date  # Update the previous date
        
    counter_values.append(counter)

# Adding a new column DAY
df['DAY'] = counter_values

# Dropping a column SESSION
if 'SESSION' in df.columns:
    df.drop(columns=['SESSION'], inplace=True)

# Adding a new column HIP
if 'hip' in df.columns:
    df['HIP'] = df['Hip']

# Adding a new column HIPNUM
if 'Hip' in df.columns:
    df['HIPNUM'] = df['Hip']

# Dropping a column HIP1
if 'Hip' in df.columns:
    df.drop(columns=['Hip'], inplace=True)

# Check if 'NAME' is a column in the DataFrame
if 'Name' in df.columns:
            # Create a new 'HORSE' column and populate it with 'NAME'
            df['HORSE'] = df['Name']
else:
    df['HORSE'] = ''

# Check if 'NAME' is a column in the DataFrame
if 'Name' in df.columns:
            # Create a new 'HORSE' column and populate it with 'NAME'
            df['CHORSE'] = df['Name']
else:
    df['CHORSE'] = ''

# Check if 'NAME' is a column in the DataFrame
if 'Name' in df.columns:
            # Dropping a column NAME
            df.drop(columns=['Name'], inplace=True)

# Adding a new column RATING
rating = ''
df['RATING'] = rating

# Adding a new column TATTOO
tattoo = ''
df['TATTOO'] = tattoo

# Adding a new column DATEFOAL
if 'Foaling Date' in df.columns:
    df['DATEFOAL'] = pd.to_datetime(df['Foaling Date'])

# Dropping a column YEAR OF BIRTH
if 'Foaling Date' in df.columns:
    df.drop(columns=['Foaling Date'], inplace=True)

# Function to calculate the age from DATEFOAL
def calculate_age(datefoal):
    today = date.today()
    born = pd.to_datetime(datefoal, errors='coerce')  # Convert to datetime, handle invalid dates
    age = today.year - born.dt.year - ((today.month * 100 + today.day) < (born.dt.month * 100 + born.dt.day))
    return age

# Calling the calculate_age() function
age = calculate_age(df['DATEFOAL'])

# Adding a new column AGE
df['AGE'] = age

# Adding a new column COLOR
if 'Color' in df.columns:
    df['COLOR'] = df['Color']

# Dropping a column COLOR1
if 'Color' in df.columns:
    df.drop(columns=['Color'], inplace=True)

# Adding a new column SEX
if 'Sex' in df.columns:
    df['SEX'] = df['Sex']

# Dropping a column SEX1
if 'Sex' in df.columns:
    df.drop(columns=['Sex'], inplace=True)

# Adding a new column GAIT
gait = ''
df['GAIT'] = gait

# Adding a new column TYPE
if 'SOLD AS CODE' in df.columns:
    df['TYPE'] = df['SOLD AS CODE']
else: 
    df['TYPE'] = 'Y'

# Adding a new column RECORD
record = ''
df['RECORD'] = record

# Adding a new column ET
et = ''
df['ET'] = et

# Replace state names in a new column 'ELIG' with state codes in the 'FOALED' column
df['ELIG'] = df['State']

df.drop(columns=['State'], inplace=True)

# Adding a new column SIRE
if 'Sire' in df.columns:
    df['SIRE'] =  df['Sire']

# Adding a new column CSIRE
if 'Sire' in df.columns:
    df['CSIRE'] = df['Sire']

# Dropping a column SIRE1
if 'Sire' in df.columns:
    df.drop(columns=['Sire'], inplace=True)

# Adding a new column DAM
if 'Dam' in df.columns:
    df['DAM'] = df['Dam']

# Adding a new column CDAM
if 'Dam' in df.columns:
    df['CDAM'] = df['Dam']

# Dropping a column DAM1
if 'Dam' in df.columns:
    df.drop(columns=['Dam'], inplace=True)

# Adding a new column SIREOFDAM
if 'Damsire' in df.columns:
    df['SIREOFDAM'] = df['Damsire']

# Adding a new column CSIREOFDAM
if 'Damsire' in df.columns:
    df['CSIREOFDAM'] = df['Damsire']

# Dropping a column SIRE OF DAM
if 'Damsire' in df.columns:
    df.drop(columns=['Damsire'], inplace=True)

# Adding a new column DAMOFDAM
damofdam = ''
df['DAMOFDAM'] = damofdam

# Adding a new column CDAMOFDAM
cdamofdam = ''
df['CDAMOFDAM'] = cdamofdam

# Adding a new column DAMTATT
damtatt = ''
df['DAMTATT'] = damtatt

# Adding a new column DAMYOF
damyof = ''
df['DAMYOF'] = damyof

# Adding a new column DDAMTATT
ddamtatt = ''
df['DDAMTATT'] = ddamtatt

# Adding a new column BREDTO
if 'Consignor' in df.columns:
    df['BREDTO'] = df['Consignor']
else:
    df['BREDTO'] = ''
# Dropping a column CONSIGNOR NAME
if 'Consignor' in df.columns:
    df.drop(columns=['Consignor'], inplace=True)

# Adding a new column LASTBRED
lastbred = ''
df['LASTBRED'] = lastbred

# Adding a new column CONLNAME
conlname = df['PROPERTY LINE']
df['CONSLNAME'] = conlname

# Dropping a column PROPERTY LINE
df.drop(columns=['PROPERTY LINE'], inplace=True)

# Adding a new column CONSNO
consno = ''
df['CONSNO'] = consno

# Adding a new column PEMCODE
pemcode = ''
df['PEMCODE'] = pemcode

# Adding a new column PURFNAME
purfname = ''
df['PURFNAME'] = purfname

df.drop(columns=['Barn'], inplace=True)

# Adding a new column PURLNAME
purlname = df['Buyer']
df['PURLNAME'] = purlname

# Dropping a column PURCHASER
df.drop(columns=['Buyer'], inplace=True)

# Adding a new column SBCITY
sbcity = ''
df['SBCITY'] = sbcity

# Adding a new column SBSTATE
sbstate = ''
df['SBSTATE'] = sbstate

# Adding a new column SBCOUNTRY
sbcountry = ''
df['SBCOUNTRY'] = sbcountry

# Adding a new column PRICE
price = df['Price']
df['PRICE'] = price

# Adding a new column PRICE1
df.drop(columns=['Price'], inplace=True)

# Adding a new column SALE TITLE
df.drop(columns=['SALE TITLE'], inplace=True)

# Adding a new column CURRENCY
currency = ''
df['CURRENCY'] = currency

# Adding a new column URL
url = df['VIRTUAL INSPECTION'] 
df['URL'] = url.fillna('')

# Dropping a column VIRTUAL INSPECTION
df.drop(columns=['VIRTUAL INSPECTION'], inplace=True)

# Adding a new column NFFM
nffm = ''
df['NFFM'] = nffm

# Adding a new column PRIVATE SALE
privatesale = df['PRIVATE SALE']
df['PRIVATESALE'] = privatesale.fillna('')
    
# Adding a new column BREED
breed = 'T'
df['BREED'] = breed

# Calculating the year of birth from the datefoal
datefoal_series = df['DATEFOAL']

# Adding a new column YEARFOAL and getting the year from DATEFOAL
df['YEARFOAL'] = df['DATEFOAL'].dt.year
