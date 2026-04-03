<?php
/**
 * Plugin Name: IPSC Events Table
 * Description: Wyświetla prostą tabelę eventów z tabeli tec_events.
 * Version: 1.0
 * Author: Bartek
 */

if (!defined('ABSPATH')) {
  $wp_load = __DIR__ . '/wp-load.php';
  if (file_exists($wp_load)) {
    require_once $wp_load;
  } else {
    exit('Brak ustawionego ABSPATH oraz pliku wp-load.php.');
  }
}

$ipsc_table_prefix = 'prec'; // Prefix niestandardowych tabel: ustaw tutaj jedną wartość
$ipsc_locale = 'pl';
if (isset($_GET['lang']) && $_GET['lang'] === 'en') {
  $ipsc_locale = 'en';
} elseif (function_exists('get_locale') && strpos(get_locale(), 'en') === 0) {
  $ipsc_locale = 'en';
}
$ipsc_translation_file = __DIR__ . '/ipsc-events-table-translations.json';
$ipsc_translations = array();
if ($ipsc_locale === 'en' && file_exists($ipsc_translation_file)) {
  $json = file_get_contents($ipsc_translation_file);
  $ipsc_translations = json_decode($json, true) ?: array();
}

function ipsc_t($key, $default = '')
{
  global $ipsc_locale, $ipsc_translations;
  if ($ipsc_locale === 'en' && isset($ipsc_translations[$key])) {
    return $ipsc_translations[$key];
  }
  return $default !== '' ? $default : $key;
}

function ipsc_styles()
{
  return '<style>
#ipsc-events-wrap {
  position: relative;
  font-family: system-ui, -apple-system, sans-serif;
  font-size: 14px;
  line-height: 1.5;
  color: #1a1a2e;
}
#ipsc-events-wrap .ipsc-lang-switcher {
  position: absolute;
  top: 20px;
  right: 20px;
  z-index: 10;
}
#ipsc-events-wrap .ipsc-lang-switcher select {
  min-width: 100px;
  padding: 6px 10px;
  border: 1px solid #c8d0df;
  border-radius: 6px;
  background: #fff;
  font-size: 13px;
}
#ipsc-events-wrap .ipsc-social-banner {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 10px;
  margin-bottom: 14px;
  padding: 12px 16px;
  background: #eef2ff;
  border: 1px solid #c7d2fe;
  border-radius: 10px;
  color: #1e3a8a;
  font-size: 14px;
}
#ipsc-events-wrap .ipsc-social-banner span {
  font-weight: 600;
}
#ipsc-events-wrap .ipsc-social-banner a {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 10px;
  border-radius: 8px;
  text-decoration: none;
  background: #fff;
  border: 1px solid #c7d2fe;
  color: #1e3a8a;
  transition: background .15s, transform .15s;
}
#ipsc-events-wrap .ipsc-social-banner a:hover {
  background: #e0e7ff;
  transform: translateY(-1px);
}
#ipsc-events-wrap .ipsc-social-icon {
  width: 18px;
  height: 18px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: inherit;
}
#ipsc-events-wrap .ipsc-social-icon svg {
  width: 18px;
  height: 18px;
  fill: currentColor;
}
#ipsc-events-wrap .ipsc-footer {
  margin-top: 22px;
  padding: 12px 16px;
  border-top: 1px solid #d6d6e6;
  color: #475569;
  background: #f8fafc;
  border-radius: 0 0 12px 12px;
  font-size: 13px;
  display: flex;
  align-items: center;
  gap: 8px;
}
#ipsc-events-wrap .ipsc-github-icon {
  width: 20px;
  height: 20px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #181717;
}
#ipsc-events-wrap .ipsc-github-icon svg {
  width: 20px;
  height: 20px;
  fill: currentColor;
}
#ipsc-events-wrap .ipsc-footer a {
  color: #2563eb;
  text-decoration: none;
}
#ipsc-events-wrap .ipsc-footer a:hover {
  text-decoration: underline;
}
#ipsc-events-wrap .ipsc-main-layout {
  display: flex;
  gap: 20px;
  align-items: flex-start;
}
#ipsc-events-wrap .ipsc-filters-sidebar {
  flex: 0 0 280px;
  min-width: 280px;
  min-height: 0;
}
#ipsc-events-wrap .ipsc-table-container {
  flex: 1;
  min-width: 0;
}
#ipsc-events-wrap .ipsc-filter-box {
  background: #f4f6fb;
  border: 1px solid #d0d7e6;
  border-radius: 8px;
  padding: 16px;
  position: sticky;
  top: 20px;
  max-height: calc(100vh - 40px);
  overflow-y: auto;
  overflow-x: hidden;
  box-sizing: border-box;
  min-height: 0;
}
#ipsc-events-wrap .ipsc-filter-section {
  margin-bottom: 12px;
}
#ipsc-events-wrap .ipsc-filter-section strong {
  display: block;
  margin-bottom: 6px;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: .05em;
  color: #4a5568;
}
#ipsc-events-wrap .ipsc-selector {
  width: 100%;
}
#ipsc-events-wrap .ipsc-selector select {
  width: 100%;
  min-height: 100px;
  padding: 6px 8px;
  border: 1px solid #c8d0df;
  border-radius: 6px;
  background: #fff;
  font-size: 13px;
  font-family: inherit;
}
#ipsc-events-wrap .ipsc-selector select:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
#ipsc-events-wrap .ipsc-selector option {
  padding: 4px 8px;
}
#ipsc-events-wrap .ipsc-selector option:checked {
  background: #2563eb;
  color: #fff;
}
#ipsc-events-wrap .ipsc-filter-actions {
  margin-top: 12px;
  display: flex;
  gap: 10px;
  align-items: center;
}
#ipsc-events-wrap .ipsc-btn {
  display: inline-block;
  padding: 7px 20px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  border: none;
  transition: background .15s;
}
#ipsc-events-wrap .ipsc-btn-primary {
  background: #2563eb;
  color: #fff;
}
#ipsc-events-wrap .ipsc-btn-primary:hover {
  background: #1d4ed8;
}
#ipsc-events-wrap .ipsc-btn-secondary {
  background: #e5e7eb;
  color: #374151;
}
#ipsc-events-wrap .ipsc-btn-secondary:hover {
  background: #d1d5db;
}
#ipsc-events-wrap .ipsc-table-wrap {
  overflow-x: auto;
}
#ipsc-events-wrap table.ipsc-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}
#ipsc-events-wrap table.ipsc-table th {
  background: #1e3a5f;
  color: #fff;
  padding: 10px 12px;
  text-align: left;
  white-space: nowrap;
  font-weight: 600;
  letter-spacing: .03em;
}
#ipsc-events-wrap table.ipsc-table td {
  padding: 8px 12px;
  vertical-align: top;
  border-bottom: 1px solid #e2e8f0;
}
#ipsc-events-wrap table.ipsc-table tbody tr:nth-child(even) td {
  background: #f8fafc;
}
#ipsc-events-wrap table.ipsc-table tbody tr:hover td {
  background: #eef4ff;
}
#ipsc-events-wrap .ipsc-date {
  white-space: nowrap;
  color: #374151;
}
#ipsc-events-wrap .ipsc-title {
  font-weight: 600;
  color: #1e3a5f;
}
#ipsc-events-wrap .ipsc-tag {
  display: inline-block;
  background: #dbeafe;
  color: #1e40af;
  border-radius: 4px;
  padding: 1px 6px;
  font-size: 11px;
  margin: 1px;
}
#ipsc-events-wrap .ipsc-cat {
  display: inline-block;
  background: #dcfce7;
  color: #166534;
  border-radius: 4px;
  padding: 1px 6px;
  font-size: 11px;
  margin: 1px;
}
#ipsc-events-wrap a.ipsc-url {
  color: #2563eb;
  word-break: break-all;
}
#ipsc-events-wrap details summary {
  cursor: pointer;
  color: #2563eb;
  font-size: 12px;
  user-select: none;
}
#ipsc-events-wrap details[open] summary {
  margin-bottom: 8px;
}
#ipsc-events-wrap .ipsc-content-wrap {
  max-width: 560px;
  overflow: auto;
  font-size: 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 8px;
  background: #fff;
}
#ipsc-events-wrap .ipsc-content-wrap table {
  border-collapse: collapse;
  width: 100%;
}
#ipsc-events-wrap .ipsc-content-wrap td {
  padding: 4px 6px;
  border-bottom: 1px solid #f0f0f0;
  vertical-align: top;
}
#ipsc-events-wrap .ipsc-content-wrap td:first-child {
  font-weight: 600;
  white-space: nowrap;
  width: 35%;
  color: #4a5568;
}
#ipsc-events-wrap .ipsc-no-results {
  padding: 20px;
  text-align: center;
  color: #6b7280;
  background: #f9fafb;
  border-radius: 8px;
  border: 1px dashed #d1d5db;
}
#ipsc-events-wrap table.ipsc-table tbody tr[data-has-content] {
  cursor: pointer;
}
/* Modal */
#ipsc-modal-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.55);
  z-index: 99999;
  align-items: center;
  justify-content: center;
  padding: 20px;
}
#ipsc-modal-overlay.ipsc-modal-open {
  display: flex;
}
#ipsc-modal-box {
  background: #fff;
  border-radius: 10px;
  max-width: 760px;
  width: 100%;
  max-height: 88vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 60px rgba(0,0,0,.3);
  overflow: hidden;
}
#ipsc-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 20px;
  background: #1e3a5f;
  color: #fff;
  flex-shrink: 0;
}
#ipsc-modal-title {
  font-size: 16px;
  font-weight: 700;
  margin: 0;
}
#ipsc-modal-close {
  background: none;
  border: none;
  color: #fff;
  font-size: 22px;
  cursor: pointer;
  line-height: 1;
  padding: 0 4px;
  opacity: .8;
}
#ipsc-modal-close:hover { opacity: 1; }
#ipsc-modal-body {
  padding: 16px 20px;
  overflow-y: auto;
  font-size: 13px;
  line-height: 1.6;
}
/* General content formatting */
#ipsc-modal-body p {
  margin: 0 0 8px;
}
#ipsc-modal-body ul,
#ipsc-modal-body ol {
  margin: 0 0 8px;
  padding-left: 20px;
}
#ipsc-modal-body li {
  margin-bottom: 3px;
}
#ipsc-modal-body b,
#ipsc-modal-body strong {
  font-weight: 700;
}
#ipsc-modal-body h1,
#ipsc-modal-body h2,
#ipsc-modal-body h3 {
  margin: 12px 0 6px;
  font-weight: 700;
  color: #1e3a5f;
}
#ipsc-modal-body a {
  color: #2563eb;
  word-break: break-all;
}
#ipsc-modal-body img {
  max-width: 100%;
  height: auto;
  display: inline-block;
}
/* Generic table (no class) */
#ipsc-modal-body table:not(.lined) {
  border-collapse: collapse;
  width: 100%;
  margin: 8px 0;
}
#ipsc-modal-body table:not(.lined) td,
#ipsc-modal-body table:not(.lined) th {
  padding: 5px 8px;
  border-bottom: 1px solid #e2e8f0;
  vertical-align: top;
}
#ipsc-modal-body table:not(.lined) td:first-child,
#ipsc-modal-body table:not(.lined) th:first-child {
  font-weight: 600;
  white-space: nowrap;
  width: 32%;
  color: #4a5568;
}
/* Styles from ipscmatch.de content */
#ipsc-modal-body table.lined {
  border-collapse: collapse;
  width: 100%;
  margin-top: 8px;
}
#ipsc-modal-body table.lined td {
  padding: 6px 10px;
  border: 1px solid #e2e8f0;
  vertical-align: top;
}
#ipsc-modal-body table.lined td:first-child {
  font-weight: 600;
  white-space: nowrap;
  width: 30%;
  background: #f8fafc;
  color: #1e3a5f;
}
#ipsc-modal-body table.lined tr:hover td {
  background: #f0f7ff;
}
#ipsc-modal-body .match_deadline_good {
  color: #15803d;
  font-weight: 600;
}
#ipsc-modal-body .match_deadline_bad {
  color: #dc2626;
  font-weight: 600;
}
#ipsc-modal-body .match_status_final {
  display: inline-block;
  background: #dcfce7;
  color: #166534;
  border: 1px solid #86efac;
  border-radius: 4px;
  padding: 2px 8px;
  font-size: 12px;
  font-weight: 600;
}
</style>';
}

function ipsc_events_table()
{
  global $wpdb, $ipsc_table_prefix, $ipsc_locale;

  $tbl_events = $ipsc_table_prefix . 'tec_events';
  $tbl_posts = $wpdb->posts;
  $tbl_tr = $wpdb->term_relationships;
  $tbl_tt = $wpdb->term_taxonomy;
  $tbl_terms = $wpdb->terms;

  // Sprawdź istnienie tabeli eventów
  $check = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $tbl_events));
  if (!$check) {
    return '<p>Brak tabeli <code>' . esc_html($tbl_events) . '</code> w bazie.</p>';
  }

  // --- Pobierz dostępne tagi (post_tag) do filtra ---
  $available_tags = $wpdb->get_results(
    "SELECT t.term_id, t.name, t.slug
     FROM {$tbl_terms} t
     JOIN {$tbl_tt} tt ON tt.term_id = t.term_id
     WHERE tt.taxonomy = 'post_tag'
     ORDER BY t.name ASC",
    ARRAY_A
  );

  // --- Pobierz dostępne kategorie (tribe_events_cat) do filtra ---
  $available_cats = $wpdb->get_results(
    "SELECT t.term_id, t.name, t.slug
     FROM {$tbl_terms} t
     JOIN {$tbl_tt} tt ON tt.term_id = t.term_id
     WHERE tt.taxonomy = 'tribe_events_cat'
     ORDER BY t.name ASC",
    ARRAY_A
  );

  // Slugi geograficzne – województwa + zagranica
  $geo_slugs = array(
    'dolnoslaskie',
    'kujawsko-pomorskie',
    'lubelskie',
    'lubuskie',
    'lodzkie',
    'malopolskie',
    'mazowieckie',
    'opolskie',
    'podkarpackie',
    'podlaskie',
    'pomorskie',
    'slaskie',
    'swietokrzyskie',
    'warminsko-mazurskie',
    'wielkopolskie',
    'zachodniopomorskie',
    'zagranica',
  );

  $available_location_cats = array();
  $available_other_cats = array();
  foreach ($available_cats as $cat) {
    if (in_array($cat['slug'], $geo_slugs, true)) {
      $available_location_cats[] = $cat;
    } else {
      $available_other_cats[] = $cat;
    }
  }

  $location_slugs_sql = "'" . implode("','", array_map('esc_sql', $geo_slugs)) . "'";

  $default_date_from = current_time('Y-m-d');
  $default_date_to = $wpdb->get_var("SELECT MAX(DATE(end_date)) FROM {$tbl_events}");
  if (empty($default_date_to)) {
    $default_date_to = $default_date_from;
  }

  // --- Odczytaj wyszukiwanie z GET ---
  $search_term = '';
  if (!empty($_GET['ipsc_search'])) {
    $search_term = sanitize_text_field($_GET['ipsc_search']);
  }

  // --- Odczytaj zakres dat z GET ---
  $date_from = '';
  $date_to = '';
  if (isset($_GET['ipsc_date_from'])) {
    $date_from = sanitize_text_field($_GET['ipsc_date_from']);
  }
  if (isset($_GET['ipsc_date_to'])) {
    $date_to = sanitize_text_field($_GET['ipsc_date_to']);
  }

  $display_date_from = $date_from !== '' ? $date_from : $default_date_from;
  $display_date_to = $date_to !== '' ? $date_to : $default_date_to;

  $filter_date_from = $date_from !== '' ? $date_from : $default_date_from;
  $filter_date_to = $date_to !== '' ? $date_to : $default_date_to;

  $page_size = 30;
  $current_page = 1;
  if (!empty($_GET['ipsc_page'])) {
    $current_page = max(1, intval($_GET['ipsc_page']));
  }
  $offset = ($current_page - 1) * $page_size;

  // --- Odczytaj wybrane tagi z GET (sanityzacja) ---
  $selected_slugs = array();
  if (!empty($_GET['ipsc_tags']) && is_array($_GET['ipsc_tags'])) {
    foreach ($_GET['ipsc_tags'] as $raw) {
      $slug = sanitize_key($raw);
      if ($slug !== '') {
        $selected_slugs[] = $slug;
      }
    }
  }

  // --- Odczytaj wybrane kategorie z GET (sanityzacja) ---
  $selected_cats = array();
  if (!empty($_GET['ipsc_cats']) && is_array($_GET['ipsc_cats'])) {
    foreach ($_GET['ipsc_cats'] as $raw) {
      $slug = sanitize_key($raw);
      if ($slug !== '') {
        $selected_cats[] = $slug;
      }
    }
  }

  // --- Odczytaj wybrane lokalizacje (województwa/zagranica) z GET ---
  $selected_locations = array();
  if (!empty($_GET['ipsc_location']) && is_array($_GET['ipsc_location'])) {
    foreach ($_GET['ipsc_location'] as $raw) {
      $slug = sanitize_key($raw);
      if ($slug !== '') {
        $selected_locations[] = $slug;
      }
    }
  }

  // --- Buduj warunek filtra ---
  $where_filter = "WHERE 1=1";

  if (!empty($selected_slugs)) {
    $placeholders = implode(', ', array_fill(0, count($selected_slugs), '%s'));
    $where_filter .= $wpdb->prepare(
      " AND e.post_id IN (
          SELECT tr2.object_id
          FROM {$tbl_tr}  tr2
          JOIN {$tbl_tt}  tt2 ON tt2.term_taxonomy_id = tr2.term_taxonomy_id
          JOIN {$tbl_terms} t2 ON t2.term_id = tt2.term_id
          WHERE tt2.taxonomy = 'post_tag'
            AND t2.slug IN ({$placeholders})
          GROUP BY tr2.object_id
          HAVING COUNT(DISTINCT t2.slug) = %d
        )",
      array_merge($selected_slugs, array(count($selected_slugs)))
    );
  }

  if (!empty($selected_cats)) {
    $placeholders = implode(', ', array_fill(0, count($selected_cats), '%s'));
    $where_filter .= $wpdb->prepare(
      " AND e.post_id IN (
          SELECT tr2.object_id
          FROM {$tbl_tr}  tr2
          JOIN {$tbl_tt}  tt2 ON tt2.term_taxonomy_id = tr2.term_taxonomy_id
          JOIN {$tbl_terms} t2 ON t2.term_id = tt2.term_id
          WHERE tt2.taxonomy = 'tribe_events_cat'
            AND t2.slug IN ({$placeholders})
          GROUP BY tr2.object_id
          HAVING COUNT(DISTINCT t2.slug) = %d
        )",
      array_merge($selected_cats, array(count($selected_cats)))
    );
  }

  // Lokalizacja – OR (event musi być w przynajmniej jednej wybranej lokalizacji)
  if (!empty($selected_locations)) {
    $placeholders = implode(', ', array_fill(0, count($selected_locations), '%s'));
    $where_filter .= $wpdb->prepare(
      " AND e.post_id IN (
          SELECT tr2.object_id
          FROM {$tbl_tr}  tr2
          JOIN {$tbl_tt}  tt2 ON tt2.term_taxonomy_id = tr2.term_taxonomy_id
          JOIN {$tbl_terms} t2 ON t2.term_id = tt2.term_id
          WHERE tt2.taxonomy = 'tribe_events_cat'
            AND t2.slug IN ({$placeholders})
        )",
      $selected_locations
    );
  }

  if ($search_term !== '') {
    $like = '%' . $wpdb->esc_like($search_term) . '%';
    $where_filter .= $wpdb->prepare(
      " AND (
          p.post_title LIKE %s
          OR p.post_content LIKE %s
          OR e.start_date LIKE %s
          OR e.end_date LIKE %s
          OR e.timezone LIKE %s
          OR EXISTS (
            SELECT 1
            FROM {$tbl_tr} tr2
            JOIN {$tbl_tt} tt2 ON tt2.term_taxonomy_id = tr2.term_taxonomy_id
            JOIN {$tbl_terms} t2 ON t2.term_id = tt2.term_id
            WHERE tr2.object_id = e.post_id
              AND t2.name LIKE %s
          )
        )",
      array($like, $like, $like, $like, $like, $like)
    );
  }

  if ($filter_date_from !== '') {
    $where_filter .= $wpdb->prepare(" AND DATE(e.start_date) >= %s", $filter_date_from);
  }
  if ($filter_date_to !== '') {
    $where_filter .= $wpdb->prepare(" AND DATE(e.start_date) <= %s", $filter_date_to);
  }

  $total_items = $wpdb->get_var(
    "SELECT COUNT(DISTINCT e.event_id) FROM {$tbl_events} e
     JOIN {$tbl_posts} p ON p.ID = e.post_id AND p.post_status = 'publish'
     LEFT JOIN {$tbl_tr} tr ON tr.object_id = e.post_id
     LEFT JOIN {$tbl_tt} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
     LEFT JOIN {$tbl_terms} t ON t.term_id = tt.term_id
     LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id = e.post_id AND pm.meta_key = '_EventURL'
     {$where_filter}"
  );
  $total_pages = max(1, (int) ceil($total_items / $page_size));
  if ($current_page > $total_pages) {
    $current_page = $total_pages;
    $offset = ($current_page - 1) * $page_size;
  }

  // --- Główne zapytanie ---
  $query = $wpdb->prepare(
    "SELECT
       e.event_id,
       e.post_id,
       p.post_title                                                              AS event_name,
       e.start_date,
       e.end_date,
       GROUP_CONCAT(DISTINCT CASE WHEN tt.taxonomy='tribe_events_cat' AND t.slug IN ({$location_slugs_sql}) THEN t.name END
                    ORDER BY t.name SEPARATOR ', ')                              AS lokalizacja,
       e.timezone,
       GROUP_CONCAT(DISTINCT CASE WHEN tt.taxonomy='tribe_events_cat' THEN t.name END
                    ORDER BY t.name SEPARATOR ', ')                              AS kategorie,
       GROUP_CONCAT(DISTINCT CASE WHEN tt.taxonomy='post_tag' THEN t.name END
                    ORDER BY t.name SEPARATOR ', ')                              AS tagi,
       MAX(CASE WHEN pm.meta_key='_EventURL'  THEN pm.meta_value END)           AS url,
       p.post_content                                                            AS post_content
     FROM {$tbl_events} e
     JOIN {$tbl_posts} p
       ON p.ID = e.post_id AND p.post_status = 'publish'
     LEFT JOIN {$tbl_tr}    tr ON tr.object_id        = e.post_id
     LEFT JOIN {$tbl_tt}    tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
     LEFT JOIN {$tbl_terms} t  ON t.term_id           = tt.term_id
     LEFT JOIN {$wpdb->postmeta} pm
       ON pm.post_id = e.post_id AND pm.meta_key = '_EventURL'
     {$where_filter}
     GROUP BY e.event_id, e.post_id, p.post_title, e.start_date, e.end_date, e.timezone, p.post_content
     ORDER BY e.start_date ASC
     LIMIT %d OFFSET %d",
    $page_size,
    $offset
  );
  $events = $wpdb->get_results($query, ARRAY_A);

  // --- CSS + wrapper ---
  $form_action = esc_url(strtok($_SERVER['REQUEST_URI'], '?'));
  $html = ipsc_styles();
  $html .= '<div id="ipsc-events-wrap">';
  $html .= '<div class="ipsc-social-banner"><span>Możesz mnie wesprzeć poprzez subskrybowanie moich social mediów</span>';
  $html .= '<a href="https://www.instagram.com/mr_bartekz/" target="_blank" rel="noopener noreferrer"><span class="ipsc-social-icon"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 7.2a4.8 4.8 0 100 9.6 4.8 4.8 0 000-9.6zm0 7.8a3 3 0 110-6 3 3 0 010 6zm5.4-8.94a1.2 1.2 0 11-2.4 0 1.2 1.2 0 012.4 0zM19.2 7.5c-.12-1.37-.36-2.32-.77-3.16a4.8 4.8 0 00-2.16-2.16c-.84-.41-1.79-.65-3.16-.77C11.8 1.2 11.07 1.2 9.9 1.2h-.1c-1.37.12-2.32.36-3.16.77a4.8 4.8 0 00-2.16 2.16c-.41.84-.65 1.79-.77 3.16C1.2 9.8 1.2 10.53 1.2 11.7v.1c.12 1.37.36 2.32.77 3.16a4.8 4.8 0 002.16 2.16c.84.41 1.79.65 3.16.77h.1c1.17 0 1.9 0 3.07-.12 1.37-.12 2.32-.36 3.16-.77a4.8 4.8 0 002.16-2.16c.41-.84.65-1.79.77-3.16v-.1c0-1.17 0-1.9-.12-3.07zM21 12.8c-.1 1.5-.34 2.58-.8 3.5a6 6 0 01-2.7 2.7c-.92.46-2 .7-3.5.8-1.16.1-1.5.1-4.5.1s-3.34 0-4.5-.1c-1.5-.1-2.58-.34-3.5-.8a6 6 0 01-2.7-2.7c-.46-.92-.7-2-.8-3.5-.1-1.16-.1-1.5-.1-4.5s0-3.34.1-4.5c.1-1.5.34-2.58.8-3.5a6 6 0 012.7-2.7c.92-.46 2-.7 3.5-.8C8.66 1.2 9 1.2 12 1.2s3.34 0 4.5.1c1.5.1 2.58.34 3.5.8a6 6 0 012.7 2.7c.46.92.7 2 .8 3.5.1 1.16.1 1.5.1 4.5s0 3.34-.1 4.5z"/></svg></span>Instagram</a>';
  $html .= '<a href="https://www.youtube.com/@Mr_BartekZ" target="_blank" rel="noopener noreferrer"><span class="ipsc-social-icon"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23.5 6.2a3 3 0 00-2.12-2.12C19.47 3.5 12 3.5 12 3.5s-7.47 0-9.38.58A3 3 0 00.5 6.2 31.3 31.3 0 000 12a31.3 31.3 0 00.5 5.8 3 3 0 002.12 2.12C4.53 20.5 12 20.5 12 20.5s7.47 0 9.38-.58A3 3 0 0023.5 17.8 31.3 31.3 0 0024 12a31.3 31.3 0 00-.5-5.8zM9.8 15.5v-7l6 3.5-6 3.5z"/></svg></span>YouTube</a>';
  $html .= '<a href="https://www.facebook.com/people/Bartek-IPSC/61587533125970/" target="_blank" rel="noopener noreferrer"><span class="ipsc-social-icon"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.675 0H1.325C.593 0 0 .593 0 1.325v21.351C0 23.406.593 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.716-1.795 1.763v2.313h3.588l-.467 3.622h-3.121V24h6.116C23.406 24 24 23.406 24 22.676V1.325C24 .593 23.406 0 22.675 0z"/></svg></span>Facebook</a>';
  $html .= '<a href="https://x.com/Mr_BartekZ" target="_blank" rel="noopener noreferrer"><span class="ipsc-social-icon"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23 5.6a1.25 1.25 0 00-1.43-.67L15 7.2 9.4 1.4a1.25 1.25 0 00-2.17.73l1.4 4.7-5 1.3a1.25 1.25 0 00-.58 2.1l7.3 6.1-3 8.2a1.25 1.25 0 002.05 1.34L12 18.3l6.3 4.4a1.25 1.25 0 002.4-.8l-2.1-8.3 4.2-1.1a1.25 1.25 0 00.7-2.2l-8.6-6.2L23 5.6z"/></svg></span>X</a>';
  $html .= '</div>';
  $html .= '<div class="ipsc-lang-switcher"><form method="get" action="' . $form_action . '" style="margin:0;">';
  foreach ($_GET as $key => $val) {
    if ($key === 'lang') {
      continue;
    }
    $key = sanitize_key($key);
    if (is_array($val)) {
      foreach ($val as $v) {
        $html .= '<input type="hidden" name="' . esc_attr($key) . '[]" value="' . esc_attr($v) . '">';
      }
    } else {
      $html .= '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '">';
    }
  }
  $html .= '<select name="lang" onchange="this.form.submit()">';
  $html .= '<option value="pl"' . ($ipsc_locale === 'pl' ? ' selected' : '') . '>🇵🇱 PL</option>';
  $html .= '<option value="en"' . ($ipsc_locale === 'en' ? ' selected' : '') . '>🇬🇧 EN</option>';
  $html .= '</select></form></div>';
  $html .= '<div class="ipsc-main-layout">';
  $html .= '<div class="ipsc-filters-sidebar">';

  // --- Formularz filtra ---
  $form_action = esc_url(strtok($_SERVER['REQUEST_URI'], '?'));
  $html .= '<form method="get" action="' . $form_action . '" class="ipsc-filter-box">';

  // przekaż query-string WordPress (page, p, itp.) jako hidden inputs
  foreach ($_GET as $key => $val) {
    if ($key === 'ipsc_tags' || $key === 'ipsc_cats' || $key === 'ipsc_location' || $key === 'ipsc_search' || $key === 'ipsc_date_from' || $key === 'ipsc_date_to') {
      continue;
    }
    $key = sanitize_key($key);
    if (is_array($val)) {
      foreach ($val as $v) {
        $html .= '<input type="hidden" name="' . esc_attr($key) . '[]" value="' . esc_attr($v) . '">';
      }
    } else {
      $html .= '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '">';
    }
  }

  $html .= '<div class="ipsc-filter-section"><strong>' . esc_html(ipsc_t('search_label', 'Wyszukaj')) . '</strong>';
  $html .= '<input type="search" name="ipsc_search" value="' . esc_attr($search_term) . '" placeholder="' . esc_attr(ipsc_t('search_placeholder', 'Wyszukaj po nazwie, opisie, tagach i lokalizacji')) . '" style="width:100%;padding:8px 10px;border:1px solid #c8d0df;border-radius:6px;font-size:14px;box-sizing:border-box;">';
  $html .= '</div>';

  $html .= '<div class="ipsc-filter-section"><strong>' . esc_html(ipsc_t('date_label', 'Data')) . '</strong><div style="display:grid;gap:8px;">';
  $html .= '<input type="date" name="ipsc_date_from" value="' . esc_attr($display_date_from) . '" style="width:100%;padding:8px 10px;border:1px solid #c8d0df;border-radius:6px;font-size:14px;box-sizing:border-box;">';
  $html .= '<input type="date" name="ipsc_date_to" value="' . esc_attr($display_date_to) . '" style="width:100%;padding:8px 10px;border:1px solid #c8d0df;border-radius:6px;font-size:14px;box-sizing:border-box;">';
  $html .= '</div></div>';

  if (!empty($available_location_cats)) {
    $html .= '<div class="ipsc-filter-section"><strong>' . esc_html(ipsc_t('location_filter_label', 'Województwo / Zagranica')) . '</strong><div class="ipsc-selector">';
    $html .= '<select name="ipsc_location[]" multiple>';
    foreach ($available_location_cats as $cat) {
      $selected = in_array($cat['slug'], $selected_locations, true) ? ' selected' : '';
      $html .= '<option value="' . esc_attr($cat['slug']) . '"' . $selected . '>' . esc_html($cat['name']) . '</option>';
    }
    $html .= '</select></div></div>';
  }

  $html .= '<div class="ipsc-filter-section"><strong>' . esc_html(ipsc_t('tags_filter_label', 'Filtruj po tagach')) . '</strong><div class="ipsc-selector">';
  $html .= '<select name="ipsc_tags[]" multiple>';
  foreach ($available_tags as $tag) {
    $selected = in_array($tag['slug'], $selected_slugs, true) ? ' selected' : '';
    $html .= '<option value="' . esc_attr($tag['slug']) . '"' . $selected . '>' . esc_html($tag['name']) . '</option>';
  }
  $html .= '</select></div></div>';

  $html .= '<div class="ipsc-filter-actions">';
  $html .= '<button type="submit" class="ipsc-btn ipsc-btn-primary">' . esc_html(ipsc_t('apply_filters', 'Zastosuj filtry')) . '</button>';
  $html .= '<a href="' . $form_action . '" class="ipsc-btn ipsc-btn-secondary">' . esc_html(ipsc_t('clear_filters', 'Wyczyść filtry')) . '</a>';
  $html .= '</div>';
  $html .= '</form>';
  $html .= '</div>'; // close .ipsc-filters-sidebar
  $html .= '<div class="ipsc-table-container">';

  // --- Tabela wyników ---
  if (empty($events)) {
    $html .= '<p class="ipsc-no-results">' . esc_html(ipsc_t('no_results', 'Brak eventów spełniających wybrane kryteria.')) . '</p>';
    $html .= '</div>'; // close .ipsc-table-container
    $html .= '</div>'; // close .ipsc-main-layout
    $html .= '</div>'; // close #ipsc-events-wrap
    return $html;
  }

  $cols = array(
    'event_name' => ipsc_t('event_name', 'Nazwa eventu'),
    'start_date' => ipsc_t('start_date', 'Data startu'),
    'end_date' => ipsc_t('end_date', 'Data końca'),
    'lokalizacja' => ipsc_t('location', 'Lokalizacja'),
    'tagi' => ipsc_t('tags', 'Tagi'),
    'url' => ipsc_t('url', 'URL'),
  );

  $html .= '<div class="ipsc-table-wrap"><table class="ipsc-table">';
  $html .= '<thead><tr>';
  foreach ($cols as $label) {
    $html .= '<th>' . esc_html($label) . '</th>';
  }
  $html .= '</tr></thead><tbody>';

  foreach ($events as $event) {
    $has_content = !empty($event['post_content']);
    $data_title = esc_attr($event['event_name']);
    $event_id = (int) $event['event_id'];
    $row_attrs = $has_content
      ? ' data-has-content="1" data-event-id="' . $event_id . '" data-title="' . $data_title . '" title="' . esc_attr(ipsc_t('click_for_details', 'Kliknij, aby zobaczyć szczegóły')) . '"'
      : '';
    $html .= '<tr' . $row_attrs . '>';
    foreach (array_keys($cols) as $field) {
      $value = isset($event[$field]) ? $event[$field] : '';

      if ($field === 'event_name') {
        $html .= '<td class="ipsc-title">' . esc_html($value) . '</td>';

      } elseif (in_array($field, array('start_date', 'end_date'), true)) {
        $parsed = strtotime($value);
        $formatted = $parsed !== false ? date_i18n('Y-m-d', $parsed) : esc_html($value);
        $html .= '<td class="ipsc-date">' . esc_html($formatted) . '</td>';

      } elseif ($field === 'lokalizacja') {
        $location_text = $value !== '' ? mb_convert_case(mb_strtolower($value, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') : ipsc_t('no_location', 'brak lokalizacji');
        $html .= '<td>' . esc_html($location_text) . '</td>';

      } elseif ($field === 'tagi') {
        $tags_out = '';
        foreach (array_filter(array_map('trim', explode(',', $value))) as $t) {
          $tags_out .= '<span class="ipsc-tag">' . esc_html($t) . '</span>';
        }
        $html .= '<td>' . $tags_out . '</td>';

      } elseif ($field === 'url') {
        if ($value !== '') {
          $html .= '<td><a class="ipsc-url" href="' . esc_url($value) . '" target="_blank" rel="noopener noreferrer">' . esc_html($value) . '</a></td>';
        } else {
          $html .= '<td>—</td>';
        }

      } else {
        $html .= '<td>' . esc_html($value) . '</td>';
      }
    }
    $html .= '</tr>';
  }

  $html .= '</tbody></table></div>';

  if ($total_pages > 1) {
    $query_args = array();
    foreach ($_GET as $key => $val) {
      if ($key === 'ipsc_page') {
        continue;
      }
      $query_args[$key] = $val;
    }
    $html .= '<div class="ipsc-pagination" style="margin:16px 0; display:flex; flex-wrap:wrap; gap:8px; align-items:center;">';
    if ($current_page > 1) {
      $query_args['ipsc_page'] = $current_page - 1;
      $html .= '<a class="ipsc-btn ipsc-btn-secondary" href="' . esc_url(add_query_arg($query_args, $form_action)) . '">' . esc_html(ipsc_t('previous', 'Poprzednia')) . '</a>';
    }
    for ($page = 1; $page <= $total_pages; $page++) {
      $query_args['ipsc_page'] = $page;
      $current = $page === $current_page ? 'background:#1d4ed8;color:#fff;' : '';
      $html .= '<a class="ipsc-btn ipsc-btn-secondary" style="padding:6px 12px; font-size:13px; ' . $current . '" href="' . esc_url(add_query_arg($query_args, $form_action)) . '">' . esc_html($page) . '</a>';
    }
    if ($current_page < $total_pages) {
      $query_args['ipsc_page'] = $current_page + 1;
      $html .= '<a class="ipsc-btn ipsc-btn-secondary" href="' . esc_url(add_query_arg($query_args, $form_action)) . '">' . esc_html(ipsc_t('next', 'Następna')) . '</a>';
    }
    $html .= '</div>';
  }

  $html .= '</div>'; // close .ipsc-table-container
  $html .= '</div>'; // close .ipsc-main-layout

  $html .= '<div class="ipsc-footer">';
  $html .= '<span class="ipsc-github-icon" aria-hidden="true"><svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.19 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/></svg></span> Projekt dostępny na GitHubie: <a href="https://github.com/bartosz-zarzyczny/ipsc-events-table" target="_blank" rel="noopener noreferrer">github.com/bartosz-zarzyczny/ipsc-events-table</a>';
  $html .= '</div>';
  // --- Ukryte divy z treścią eventów ---
  $html .= '<div style="display:none" aria-hidden="true">';
  foreach ($events as $event) {
    if (!empty($event['post_content'])) {
      $event_id = (int) $event['event_id'];
      // Treść blokowa (tabela/div) - renderuj jako HTML bez modyfikacji.
      // Treść z inline tagami lub czysty tekst - zastosuj nl2br.
      $is_block_html = preg_match('/<(table|thead|tbody|tr|td|div|ul|ol|li|h[1-6]|blockquote)[\s>]/i', $event['post_content']);
      $content = wp_kses_post($event['post_content']);
      if (!$is_block_html) {
        $content = nl2br($content);
      }
      $html .= '<div id="ipsc-content-' . $event_id . '">' . $content . '</div>';
    }
  }
  $html .= '</div>';

  // --- Modal ---
  $html .= '<div id="ipsc-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="ipsc-modal-title">';
  $html .= '<div id="ipsc-modal-box">';
  $html .= '<div id="ipsc-modal-header">';
  $html .= '<h3 id="ipsc-modal-title"></h3>';
  $html .= '<button id="ipsc-modal-close" aria-label="Zamknij">&times;</button>';
  $html .= '</div>';
  $html .= '<div id="ipsc-modal-body"></div>';
  $html .= '</div></div>';

  // --- JS ---
  $html .= "<script>
(function(){
  var overlay  = document.getElementById('ipsc-modal-overlay');
  var title    = document.getElementById('ipsc-modal-title');
  var body     = document.getElementById('ipsc-modal-body');
  var closeBtn = document.getElementById('ipsc-modal-close');

  document.querySelectorAll('#ipsc-events-wrap tr[data-has-content]').forEach(function(row){
    row.addEventListener('click', function(e){
      if (e.target.closest('a')) return;
      var src = document.getElementById('ipsc-content-' + row.dataset.eventId);
      if (!src) return;
      title.textContent = row.dataset.title;
      body.innerHTML = src.innerHTML;
      overlay.classList.add('ipsc-modal-open');
      document.body.style.overflow = 'hidden';
    });
  });

  function closeModal(){
    overlay.classList.remove('ipsc-modal-open');
    document.body.style.overflow = '';
  }
  closeBtn.addEventListener('click', closeModal);
  overlay.addEventListener('click', function(e){
    if (e.target === overlay) closeModal();
  });
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') closeModal();
  });
})();
</script>";

  $html .= '</div>'; // close #ipsc-events-wrap

  return $html;
}

function ipsc_events_table_shortcode()
{
  return ipsc_events_table();
}
add_shortcode('ipsc_events_table', 'ipsc_events_table_shortcode');

if (!empty($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === basename(__FILE__)) {
  echo '<!doctype html><html><head><meta charset="utf-8"><title>IPSC events</title></head><body>';
  echo ipsc_events_table();
  echo '</body></html>';
  exit;
}
