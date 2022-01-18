import pandas as pd


def csv_to_dataframe(csv_file):
    """
    Takes in a csv file and turns it into a dataframe.
    :param csv_file: the csv file
    :return: the dataframe
    """
    return pd.read_csv(csv_file, encoding='utf-8')


def main():
    return


if __name__ == '__main__':
    main()