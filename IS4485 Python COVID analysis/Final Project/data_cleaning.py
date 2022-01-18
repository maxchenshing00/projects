import pandas as pd
from datetime import datetime
import data_parsing as dp


def string_to_date(string_date):
    """
    Takes in a string formatted as "mm/dd/yy" and converts it into a datetime object.
    Meant for both data types.
    :param string_date: string formatted as "mm/dd/yy"
    :return: datetime object
    """
    if '/' in string_date:
        date_list = string_date.split('/')

        month = int(date_list[0])
        day = int(date_list[1])
        year = int("20" + date_list[2])

        return datetime(year, month, day)
    else:
        date_list = string_date.split('-')

        month = int(date_list[1])
        day = int(date_list[2])
        year = int(date_list[0])

        return datetime(year, month, day)


def find_nulls(dataframe, country_index=None):
    """
    Finds the null columns, null types, and null indexes of a dataframe.
    Optionally finds the country associated with the null.
    Meant for both data types.
    :param dataframe: the dataframe
    :return: formatted string
    """
    null_text = ""
    count = 0
    for index, row in dataframe.iterrows():
        for key, val in row.iteritems():
            if pd.isnull(val):
                count += 1
                if country_index is None:
                    continue
                    null_text += "col:{:<40} type:{:<5} index:{:<7} \n".format(key, val, index)
                else:
                    null_text += "col:{:<40} type:{:<5} index:{:<7} country:{} \n".format(key, val, index,
                                                                                          row[country_index])
    null_text += "Count: {} \n".format(count)
    return null_text


def add_country_total_rows(dataframe):
    """
    If a country with provinces does not have its own individual values,
    then a new row is added that is the sum of the country's provinces.
    Meant for data type 1.
    :param dataframe: the dataframe
    :return: updated dataframe
    """
    for index, row in dataframe.iterrows():
        if pd.isnull(row['Province/State']):
            continue
        else:
            df = dataframe.loc[dataframe['Country/Region'] == row["Country/Region"]]
            val = df.iloc[-1:]['Province/State'].values[0]
            if isinstance(val, str):
                # Find sums of all provinces
                sums = df.iloc[:, 4:].sum(axis=0)
                sums = list(sums.values)

                # Create as lists the columns and rows
                dict_col = list(dataframe.columns.values)
                dict_row = [float("NaN"), row["Country/Region"], float("NaN"), float("NaN")]
                dict_row.extend(sums)

                # Use the lists to create dictionary
                new_dict = dict(zip(dict_col, dict_row))

                # Add new row
                dataframe = dataframe.append(new_dict, ignore_index=True)
    dataframe.sort_values('Country/Region')
    return dataframe


def find_rows_not_number(series):
    """
    Finds index and values of a column that are not numbers.
    Meant for data type 2.
    :param series: the series, the column
    """
    for key, val in series.iteritems():
        if pd.isnull(val):
            continue
        elif not val.isdecimal():
            print("index:", key, "value:", val)
    return


def remove_trailing_zero(dataframe, country):
    """
    Takes in a dataframe and removes trailing 0's
    Meant for data type 2.
    :param dataframe: the dataframe
    :param country: string country name
    :return: the updated dataframe
    """
    df = dataframe.loc[dataframe['location'] == country]
    df_vals = list(df.iloc[:, 2:].values[0])

    num_zeroes = 0
    for item in reversed(df_vals):
        if item == 0:
            num_zeroes += 1
        elif item != 0 and num_zeroes == 0:
            break
        else:
            break

    if num_zeroes != 0:
        df = df.iloc[:, :-num_zeroes]

    return df


def create_x(is_vax, dataframe, country):
    """
    Creates x values list.
    Meant for both data types.
    :param is_vax: boolean that identifies data type
    :param dataframe: the dataframe
    :param country: the country in the dataframe
    :return: x list
    """
    if not is_vax:
        # Create x list
        x_lst = list(dataframe.columns.values)[5:]
        x_lst = [string_to_date(x) for x in x_lst]
        return x_lst
    else:
        # Remove trailing 0's for particular row
        df = dataframe.loc[dataframe['location'] == country]
        df = remove_trailing_zero(df, country)

        # Create x list
        x_lst = list(df.columns.values)[2:]
        x_lst = [string_to_date(x) for x in x_lst]
        return x_lst


def create_y(is_vax, dataframe, place, is_province=False):
    """
    Creates y values list.
    Meant for both data types.
    :param is_vax: boolean that identifies data type
    :param dataframe: the dataframe
    :param place: the country OR province
    :param is_province: True if 'place' parameter is a province
    :return: y list
    """
    if not is_vax:
        if not is_province:
            # Create y list
            df = dataframe.loc[dataframe['Country/Region'] == place]
            df = df[df['Province/State'].isna()]
            y_lst = df.values.tolist()
            y_lst = y_lst[0][5:]
            return y_lst
        else:
            # Create y list
            y_lst = dataframe.loc[dataframe['Province/State'] == place].values.tolist()
            y_lst = y_lst[0][5:]
            return y_lst
    else:
        # Remove trailing 0's for particular row
        dataframe = dataframe.loc[dataframe['location'] == place]
        dataframe = remove_trailing_zero(dataframe, place)

        # Create y list
        y_lst = dataframe.loc[dataframe['location'] == place].values.tolist()
        y_lst = y_lst[0][2:]
        return y_lst


def data_clean(csv_file):
    """
    Cleans the data from csv file into pandas dataframe.
    :param csv_file: string of the csv file
    :return: list of the dataframes
    """

    df_lst = list()

    if not csv_file == "data.csv":
        # Create dataframe
        df = dp.csv_to_dataframe(csv_file)

        # Add country total rows
        df = add_country_total_rows(df)

        # Add totals column
        df['Total'] = 0

        # # Reorganize columns
        cols = list(df.columns.values)
        df = df[cols[0:4] + [cols[-1]] + cols[4:-1]]

        # Updating Totals
        for index, row in df.iterrows():
            s = df.loc[index]
            s_vals = s.values.tolist()
            s_vals = s_vals[5:]

            num_zeroes = 0
            for item in reversed(s_vals):
                if item == 0:
                    num_zeroes += 1
                elif item != 0 and num_zeroes == 0:
                    break
                else:
                    break

            if num_zeroes != 0:
                df.at[index, 'Total'] = s[:-num_zeroes].iloc[-1]
            elif num_zeroes == 0:
                df.at[index, 'Total'] = s[:].iloc[-1]

        # If csv_file is recovered data, remove trailing 0's
        if csv_file == "time_series_covid19_recovered_global.csv":
            df = df.iloc[:, :-90]

        # Add dataframe(s) to list
        df_lst.append(df)

        return df_lst
    else:
        # Create dataframe
        df = dp.csv_to_dataframe('data.csv')

        # Remove first row
        df = df.iloc[1:, :]

        # Change data type to float
        df.total_vaccinations = df.total_vaccinations.astype(float)
        df.daily_vaccinations = df.daily_vaccinations.astype(float)

        # Create 3 dataframes
        df_totalvax = df.pivot(index='location', columns='date', values='total_vaccinations')
        df_totalvax.reset_index(level=0, inplace=True)
        df_totalvax = df_totalvax.rename_axis(None, axis=1)
        df_dailyvax = df.pivot(index='location', columns='date', values='daily_vaccinations')
        df_dailyvax.reset_index(level=0, inplace=True)
        df_dailyvax = df_dailyvax.rename_axis(None, axis=1)
        df_vaxpermil = df.pivot(index='location', columns='date', values='daily_vaccinations_per_million')
        df_vaxpermil.reset_index(level=0, inplace=True)
        df_vaxpermil = df_vaxpermil.rename_axis(None, axis=1)

        # Add total column
        df_totalvax['Total'] = 0  # Total not applicable to total_vaccinations
        df_dailyvax['Total'] = df_dailyvax.iloc[:, 1:].sum(axis=1)
        df_vaxpermil['Total'] = 0  # Total not applicable to daily_vaccinations_per_million

        # Reorganize columns
        cols_totalvax = list(df_totalvax.columns.values)
        df_totalvax = df_totalvax[cols_totalvax[0:1] + [cols_totalvax[-1]] + cols_totalvax[1:-1]]
        cols_dailyvax = list(df_dailyvax.columns.values)
        df_dailyvax = df_dailyvax[cols_dailyvax[0:1] + [cols_dailyvax[-1]] + cols_dailyvax[1:-1]]
        cols_vaxpermil = list(df_vaxpermil.columns.values)
        df_vaxpermil = df_vaxpermil[cols_vaxpermil[0:1] + [cols_vaxpermil[-1]] + cols_vaxpermil[1:-1]]

        # Change NaN to 0.0
        df_totalvax = df_totalvax.iloc[:, :].fillna(0.0)
        df_dailyvax = df_dailyvax.iloc[:, :].fillna(0.0)
        df_vaxpermil = df_vaxpermil.iloc[:, :].fillna(0.0)

        # Add dataframe(s) to list
        df_lst.append(df_totalvax)
        df_lst.append(df_dailyvax)
        df_lst.append(df_vaxpermil)
        return df_lst


def main():
    return


if __name__ == '__main__':
    main()