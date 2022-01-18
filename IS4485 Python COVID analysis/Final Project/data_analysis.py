from matplotlib.pyplot import subplot
import data_cleaning as dc
import difflib
from matplotlib import pyplot as plt
import numpy as np


def country_exists(is_vax, country, dataframe):
    """
    Returns true if country exists in a dataframe, returns false otherwise.
    :param is_vax: boolean true if dataframe is related to vaccination
    :param country: the country
    :param dataframe: the dataframe
    :return: True if the country exists in the dataframe
    """
    if not is_vax:
        return True if country in dataframe['Country/Region'].values else False
    else:
        return True if country in dataframe['location'].values else False


def province_exists(province, dataframe):
    """
    Returns true if province exists in a dataframe, returns false otherwise.
    :param province: the province
    :param dataframe: the dataframe
    :return: True if province exists in dataframe
    """
    if 'Country/Region' not in dataframe.columns:
        return False

    return True if province in dataframe['Province/State'].values else False


def provinces_in_country(country, dataframe):
    """
    Returns list of provinces in a country.
    :param country: the country
    :param dataframe: the dataframe
    :return: the list of provinces
    """
    provinces = []
    if 'Country/Region' not in dataframe.columns or not country_exists(False, country, dataframe):
        return provinces
    else:
        provinces = list(dataframe.loc[dataframe['Country/Region'] == country]['Province/State'].values)
        provinces = [prov for prov in provinces if isinstance(prov, str)]
        return provinces


def province_close_matches(province, dataframe):
    """
    Returns a list of close matches to a given province.
    :param province: the given province
    :param dataframe: the dataframe
    :return: the list of close matches to given province
    """
    close_matches = []
    if 'Province/State' not in dataframe.columns:
        return close_matches
    else:
        all_provinces = list(dataframe['Province/State'].values)
        all_provinces = [prov for prov in all_provinces if isinstance(prov, str)]
        close_matches = difflib.get_close_matches(province, all_provinces, 3, 0.2)
        return close_matches


def country_close_matches(country, dataframe):
    """
    Returns a list of close matches to a given country.
    :param country: the given country
    :param dataframe: the dataframe
    :return: the list of close matches to given country
    """
    if 'Country/Region' not in dataframe.columns:
        all_countries = set(dataframe['location'].values)
        close_matches = difflib.get_close_matches(country, all_countries, 3, 0.2)
        return close_matches
    else:
        all_countries = set(dataframe['Country/Region'].values)
        close_matches = difflib.get_close_matches(country, all_countries, 3, 0.2)
        return close_matches


def find_mean(is_vax, country, dataframe):
    """
    Find the mean for a specific country and dataframe
    :param is_vax: True if dataframe is vaccination related
    :param country: the name of the country
    :param dataframe: the dataframe
    :return: The mean
    """
    if not country_exists(is_vax, country, dataframe):
        return country_close_matches(country, dataframe)

    if is_vax:
        df = dataframe.loc[dataframe['location'] == country]
        length = len(df.columns) - 2
        total = df['Total']
        avg = total / length
        return avg.tolist()[0]
    else:
        df = dataframe.loc[dataframe['Country/Region'] == country]
        df = df[df['Province/State'].isna()]
        length = len(df.columns) - 5
        total = df['Total']
        avg = total / length
        return avg.tolist()[0]


def create_labels_lst(countries, novax_lst, vax_lst):
    """
    Creates a labels list for create_line_graph method
    :param countries: ordered list of countries
    :param novax_lst: ordered list of non vaccination variables
    :param vax_lst: ordered list of vaccination variables
    :return: uniquely ordered list for create_line_graph method
    """
    labels = []
    for country in countries:
        for category in novax_lst:
            labels.append(country + " " + category)

    for country in countries:
        for category in vax_lst:
            labels.append(country + " " + category)

    return labels


def create_line_graph(novax_country_lst, novax_df_lst, vax_country_lst, vax_df_lst, labels, title="",
                      x_label="", y_label=""):
    """
    Creates a timeseries line graph.
    :param novax_country_lst: list of country names in non vaccination related dataframes
    :param novax_df_lst: list of non vaccination related dataframes
    :param vax_country_lst: list of country names in vaccination related dataframes
    :param vax_df_lst: list of vaccination related dataframes
    :param labels: uniquely ordered labels list
    :param title: the title
    :param x_label: the x axis label
    :param y_label: the y axis label
    :return: If country is not found, then return list of close matches, else create timeseries plot
    """
    # Check if country exists
    for country in novax_country_lst:
        for df in novax_df_lst:
            if not country_exists(False, country, df):
                return country_close_matches(country, df)

    for country in vax_country_lst:
        for df in vax_df_lst:
            if not country_exists(True, country, df):
                return country_close_matches(country, df)

    # Style
    plt.style.use('ggplot')

    # Find max y value for y axis ticks
    max_y = 0;
    for country in novax_country_lst:
        for df in novax_df_lst:
            df = df.loc[df['Country/Region'] == country]
            df = df.drop(["Province/State", "Country/Region", "Lat", "Long", "Total"], axis=1)
            max_val = df.max(axis=1).values[0]
            if max_val > max_y:
                max_y = max_val

    for country in vax_country_lst:
        for df in vax_df_lst:
            df = df.loc[df['location'] == country]
            df = df.drop(["location", "Total"], axis=1)
            max_val = df.max(axis=1).values[0]
            if max_val > max_y:
                max_y = max_val

    # Format y axis ticks
    y_ticks = np.arange(0, max_y, max_y / 15)
    plt.yticks(y_ticks)

    # Create the graph
    i = 0
    for country in novax_country_lst:
        for df in novax_df_lst:
            plt.plot_date(dc.create_x(False, df, country), dc.create_y(False, df, country), fmt=',-', label=labels[i])
            i += 1

    for country in vax_country_lst:
        for df in vax_df_lst:
            plt.plot_date(dc.create_x(True, df, country), dc.create_y(True, df, country), fmt=',-', label=labels[i])
            i += 1

    # Label graph
    plt.title(title)
    plt.xlabel(x_label)
    plt.ylabel(y_label)

    # Creating sorted legend
    plt.legend()
    ax = subplot(1, 1, 1)
    handles, labels = ax.get_legend_handles_labels()
    labels, handles = zip(*sorted(zip(labels, handles), key=lambda t: t[0]))
    ax.legend(handles, labels)

    # Format dates on x axis
    plt.gcf().autofmt_xdate()

    # Show plot
    plt.show()
    return


def get_total(is_vax, country, dataframe):
    """
    Return the total value from Total column in dataframe for a given country
    :param is_vax: True if dataframe is vaccination related, False otherwise
    :param country: the name of the country
    :param dataframe: the dataframe
    :return: the total value
    """
    if not country_exists(is_vax, country, dataframe):
        return country_close_matches(country, dataframe)

    if is_vax:
        df = dataframe.loc[dataframe['location'] == country]
        total = df['Total'].tolist()[0]
        return total
    else:
        df = dataframe.loc[dataframe['Country/Region'] == country]
        df = df[df['Province/State'].isna()]
        total = df['Total'].tolist()[0]
        return total


def get_total_lst(is_vax, country_lst, dataframe):
    """
    Return list of all total values given a list of countries
    :param is_vax: True if dataframe is vaccination related, False otherwise
    :param country_lst: list of country names
    :param dataframe: the dataframe
    :return: the specially ordered list of total values for given countries
    """
    lst = []

    for country in country_lst:
        lst.append(get_total(is_vax, country, dataframe))

    return lst


def get_mean_lst(is_vax, country_lst, dataframe):
    """
    Return list of all average values given a list of countries
    :param is_vax: True if dataframe is vaccination related, False otherwise
    :param country_lst: list of country names
    :param dataframe: the dataframe
    :return: the specially ordered list of average values for given countries
    """

    lst = []

    for country in country_lst:
        lst.append(find_mean(is_vax, country, dataframe))

    return lst


def main():
    return


if __name__ == '__main__':
    main()
