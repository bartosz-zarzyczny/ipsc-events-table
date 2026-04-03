# Tabela eventów IPSC

Repozytorium zawiera prosty plik do WordPress do pluginu https://theeventscalendar.com/ `ipsc-events-table.php`, który renderuje tabelę eventów z filtrami i paginacją.

## Działanie

Plugin wyświetla tabelę eventów z niestandardowej tabeli `tec_events`, połączoną z postami WordPress i danymi taxonomii. Zapewnia:

- listę eventów z kolumnami:
  - Nazwa eventu
  - Data startu
  - Data końca
  - Lokalizacja
  - Tagi
  - URL
- filtry:
  - wyszukiwanie tekstowe w tytule, opisie eventu, dacie, strefie czasowej oraz tagach/kategoriach
  - filtrowanie po zakresie dat
  - wybór lokalizacji (województwo / zagranica)
  - wybór tagów
- paginację po 30 rekordów na stronę
- modal z rozwijanym opisem eventu
- obsługę shortcode do osadzenia tabeli na stronie/postie

## Instalacja

1. Skopiuj `ipsc-events-table.php` do katalogu strony WordPress.
2. Dołącz plik z poziomu wtyczki lub motywu, jeśli nie jest już ładowany.
3. Wstaw shortcode `[ipsc_events_table]` na stronie lub w poście, aby wyświetlić tabelę.

## Konfiguracja

### Prefiks tabeli

Plugin pozwala ustawić prefiks dla niestandardowej tabeli eventów.

Edytuj jedną zmienną w górnej części `ipsc-events-table.php`:

```php
$ipsc_table_prefix = 'prec'; // Prefiks niestandardowych tabel: ustaw tutaj jedną wartość
```

Jeśli instalacja używa innego prefiksu, zmień tę wartość, a plugin użyje go przy budowaniu nazwy tabeli.

### Domyślne zachowanie dat

Przy pierwszym wejściu na stronę bez wybranych filtrów daty, tabela domyślnie ustawi:

- `Data od` = bieżąca data
- `Data do` = najpóźniejsza data `end_date` z tabeli eventów

Jeśli użytkownik poda własne wartości dat, zostaną one użyte zamiast domyślnych.

## Przegląd kodu

### Główne funkcje

- `ipsc_styles()`
  - zwraca style CSS dla tabeli, filtrów i modala.

- `ipsc_events_table()`
  - buduje formularz filtrów, zapytanie SQL oraz generuje HTML tabeli.
  - odczytuje parametry GET: `ipsc_search`, `ipsc_date_from`, `ipsc_date_to`, `ipsc_location[]`, `ipsc_tags[]` i `ipsc_page`.
  - tworzy zapytanie SQL z logiką filtrowania i paginacją.
  - renderuje HTML tabeli oraz ukryte treści dla modala.

- `ipsc_events_table_shortcode()`
  - zwraca wynik `ipsc_events_table()` do użycia w shortcode `[ipsc_events_table]`.

## Shortcode

Wstaw ten shortcode na dowolną stronę lub do posta:

```text
[ipsc_events_table]
```

## Uwagi

- Kod korzysta z `wpdb` do dostępu do bazy danych i standardowej sanitizacji WordPress.
- Niestandardowa tabela eventów jest traktowana jako `${prefix}tec_events`, gdzie `${prefix}` ustawia się w `$ipsc_table_prefix`.
- Plugin oczekuje istnienia tabel taksonomii WordPress dla danych `tribe_events_cat` i `post_tag`.

## Dodatkowe zmiany

- Dodano górny baner z wezwaniem do subskrybowania mediów społecznościowych oraz linkami do profili Instagram, YouTube, Facebook i X.
- Dodano stopkę na dole strony z odnośnikiem do repozytorium GitHub.
- Dodano pliki licencji `LICENSE_PL.md` i `LICENSE_EN.md`, które zawierają zapis o udostępnieniu kodu "as is", wyłączeniu odpowiedzialności oraz zakazie usuwania linków do social mediów i GitHub bez zgody autora.
