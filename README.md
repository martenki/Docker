# Autorent

PHP 8.2, MySQL 8 ja Bootstrap 5 abil tehtud õppimiseks mõeldud autorendi veebirakendus.

## Käivitamine Dockeriga

1. Paigalda ja käivita Docker Desktop.
2. Ava projekti kaust terminalis ja käivita:

   ```powershell
   docker compose up -d --build
   ```

3. Ava veebileht: <http://localhost>
4. Ava andmebaasi haldus: <http://localhost:8080>

Rakendus kasutab veebikonteinerist andmebaasi hostinime `db`; Windowsi `localhost` ei sobi konteineritevaheliseks ühenduseks.

### Vaikekontod

- Admini haldus: kasutaja `admin`, parool `admin` aadressil <http://localhost/admin/login.php>.
- phpMyAdmin: kasutaja `appuser`, parool `appuserpass`, andmebaas `autorent_db`.
- Andmebaasi kasutajanimi ja parool on õppetöö jaoks näidisväärtused. Muuda need enne avalikku kasutamist.

Kasutaja saab konto luua menüüst **Loo konto**. Broneeringu tegemiseks peab kasutaja olema sisse loginud.

## Funktsioonid

- Responsive Bootstrapi avaleht ja autokaardid.
- Otsing auto margi ja mudeli järgi ning toimiv lehekülgede kaupa kuvamine.
- Auto detailvaade koos tehniliste andmete ja päeva hinnaga.
- Kasutaja registreerimine, sisselogimine ja turvaliselt räsi kujul salvestatud paroolid.
- Kuupäevade valimine, rendihinna arvutamine ning kattuvate broneeringute vältimine.
- Admini sessioonikaitse ning autode lisamine, muutmine ja kustutamine.
- CSRF-kaitse vormidel ja SQL päringud seotud parameetritega.

Rendi algus- ja lõppkuupäev loetakse mõlemad rendipäevadeks. Näiteks 1.–3. kuupäev on kolm päeva.

## Andmebaas ja migratsioonid

Uues andmebaasimahus laetakse `car_rent.sql` näidisautod ning `database/migrations/001_rental_schema.sql` lisab `cars` tabeli väljad ja `users`/`reservations` tabelid. Migratsioon on korduvkäivitamisel ohutu ning olemasolevaid autosid ei kustuta.

Kui sul oli andmebaasimahu käivituseelne versioon, käivita uuendus käsitsi:

```powershell
docker compose exec -T db sh -c 'mysql -uappuser -pappuserpass autorent_db < /docker-entrypoint-initdb.d/99_rental_schema.sql'
```

Andmed säilivad Docker named volume'is `db_data`. Rakenduse tavalisel taaskäivitamisel kasuta `docker compose down` ja `docker compose up -d`; **ära kasuta `docker compose down -v`**, kui soovid andmebaasi andmed alles hoida.

Logide vaatamine:

```powershell
docker compose ps
docker compose logs -f web
docker compose logs -f db
```

## Failide ülevaade

- `index.php` — avaleht, otsing ja autode lehekülgede kaupa kuvamine.
- `auto.php` — auto detailid ja broneerimine.
- `regamine.php`, `login.php`, `logout.php` — kasutajakonto toimingud.
- `admin/` — admini sisselogimine ja autode haldus.
- `config.php`, `inc/` — andmebaasiühendus ja jagatud abifunktsioonid.
- `car_rent.sql`, `database/migrations/` — näidisandmed ja andmebaasi struktuur.
