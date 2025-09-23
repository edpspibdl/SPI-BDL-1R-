import psycopg2

def get_conn():
    return psycopg2.connect(
        host="172.31.146.253",
        port="5432",
        database="spibdl1r",
        user="edp",
        password="3dp1grVIEW"
    )
