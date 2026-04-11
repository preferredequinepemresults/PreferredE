import pandas as pd
from datetime import date

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

# # Initialize a counter
# counter = 0

# # Initialize a list to store the counter values
# counter_values = []

# # Iterate through the list of dates
# for i, date_str in enumerate(df['SESSION']):
#     # Convert the date string to a datetime object
#     date = datetime.strptime(date_str, '%Y-%m-%d')
    
#     # Check if this is the first date or if the date has changed from the previous one
#     if i == 0 or date != prev_date:
#         counter += 1  # Increment the counter when the date changes
#         prev_date = date  # Update the previous date
        
#     counter_values.append(counter)

# Adding a new column DAY
df['DAY'] = 1

# Dropping a column SESSION
if 'Day' in df.columns:
    df.drop(columns=['Day'], inplace=True)

# Adding a new column HIP
if 'Lot' in df.columns:
    df['HIP'] = df['Lot']

# Adding a new column HIPNUM
if 'Lot' in df.columns:
    df['HIPNUM'] = df['Lot']

# Dropping a column HIP1
if 'Lot' in df.columns:
    df.drop(columns=['Lot'], inplace=True)

# Check if 'NAME' is a column in the DataFrame
if 'Nom' in df.columns:
            # Create a new 'HORSE' column and populate it with 'NAME'
            df['HORSE'] = df['Nom']
else:
    df['HORSE'] = ''

# Check if 'NAME' is a column in the DataFrame
if 'Nom' in df.columns:
            # Create a new 'HORSE' column and populate it with 'NAME'
            df['CHORSE'] = df['Nom']
else:
    df['CHORSE'] = ''

# Check if 'NAME' is a column in the DataFrame
if 'Nom' in df.columns:
            # Dropping a column NAME
            df.drop(columns=['Nom'], inplace=True)

# Adding a new column RATING
rating = ''
df['RATING'] = rating

# Adding a new column TATTOO
tattoo = ''
df['TATTOO'] = tattoo

# Adding a new column DATEFOAL
datefoal = df['Date de naissance']
df['DATEFOAL'] = datefoal

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

df.drop(columns=['Date de naissance'], inplace=True)

# Adding a new column COLOR
if 'Colour' in df.columns:
    df['COLOR'] = df['Colour']

# Dropping a column COLOR1
if 'Colour' in df.columns:
    df.drop(columns=['Colour'], inplace=True)

# Adding a new column SEX
if 'Sexe' in df.columns:
    df['SEX'] = df['Sexe']

# Dropping a column SEX1
if 'Sexe' in df.columns:
    df.drop(columns=['Sexe'], inplace=True)

# Adding a new column GAIT
gait = ''
df['GAIT'] = gait

# Adding a new column TYPE
condition_covered_by = df['Covered by'].notna()
condition_foal = df['Produit'] == 'foal'
# Define choices
choices = np.select(
    [condition_covered_by, condition_foal],
    ['B', 'W'],
    default=''
)

# Assign the result to the 'TYPE' column
df['TYPE'] = choices

# Adding a new column RECORD
record = ''
df['RECORD'] = record

# Adding a new column ET
et = ''
df['ET'] = et

# Replace state names in a new column 'ELIG' with state codes in the 'FOALED' column
df['ELIG'] = ''

# Adding a new column SIRE
if 'Père' in df.columns:
    df['SIRE'] =  df['Père']

# Adding a new column CSIRE
if 'Père' in df.columns:
    df['CSIRE'] = df['Père']

# Dropping a column SIRE1
if 'Père' in df.columns:
    df.drop(columns=['Père'], inplace=True)

# Adding a new column DAM
if 'Mère' in df.columns:
    df['DAM'] = df['Mère']

# Adding a new column CDAM
if 'Mère' in df.columns:
    df['CDAM'] = df['Mère']

# Dropping a column DAM1
if 'Mère' in df.columns:
    df.drop(columns=['Mère'], inplace=True)

df.drop(columns=['Produit'], inplace=True)

# Adding a new column SIREOFDAM
if 'Père de Mère' in df.columns:
    df['SIREOFDAM'] = df['Père de Mère']

# Adding a new column CSIREOFDAM
if 'Père de Mère' in df.columns:
    df['CSIREOFDAM'] = df['Père de Mère']

# Dropping a column SIRE OF DAM
if 'Père de Mère' in df.columns:
    df.drop(columns=['Père de Mère'], inplace=True)

df.drop(columns=['Issue'], inplace=True)
df.drop(columns=['Cour / Box'], inplace=True)
df.drop(columns=['Suffixe'], inplace=True)
df.drop(columns=['Suffixe Père'], inplace=True)
df.drop(columns=['Suffixe Mère'], inplace=True)

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
if 'Pleine de' in df.columns:
    df['BREDTO'] = df['Pleine de'].fillna("")

# Dropping a column CONSIGNOR NAME
if 'Pleine de' in df.columns:
    df.drop(columns=['Pleine de'], inplace=True)

# Adding a new column LASTBRED
lastbred = ''
df['LASTBRED'] = lastbred

# Adding a new column CONLNAME
conlname = df["Vendeur"]
df['CONSLNAME'] = conlname

# Dropping a column PROPERTY LINE
df.drop(columns=['Vendeur'], inplace=True)

# Adding a new column CONSNO
consno = ''
df['CONSNO'] = consno

# Adding a new column PEMCODE
pemcode = ''
df['PEMCODE'] = pemcode

# Adding a new column PURFNAME
purfname = ''
df['PURFNAME'] = purfname

# Adding a new column PURLNAME
purlname = df['Acheteur']
df['PURLNAME'] = purlname

# Dropping a column PURCHASER
df.drop(columns=['Acheteur'], inplace=True)

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
price = df['Enchères']
df['PRICE'] = price

# Adding a new column PRICE1
df.drop(columns=['Enchères'], inplace=True)

# Adding a new column CURRENCY
currency = ''
df['CURRENCY'] = currency

# Adding a new column URL
url = ''
df['URL'] = url.fillna('')

# Adding a new column NFFM
nffm = ''
df['NFFM'] = nffm

# Adding a new column PRIVATE SALE
privatesale = ''
df['PRIVATESALE'] = privatesale.fillna('')
    
# Adding a new column BREED
breed = 'T'
df['BREED'] = breed

# Calculating the year of birth from the datefoal
datefoal_series = df['DATEFOAL'].dt.year

# Adding a new column YEARFOAL and getting the year from DATEFOAL
df['YEARFOAL'] = df['DATEFOAL'].dt.year
