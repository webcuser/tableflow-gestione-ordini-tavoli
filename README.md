# tableflow-gestione-ordini-tavoli

Web app SaaS per gestione digitale di ordini e tavoli nei ristoranti.

## Overview

# Product Requirements Document (PRD)

## 1. Project Overview

**Project Name:** tableflow-gestione-ordini-tavoli

TableFlow è una piattaforma SaaS per la digitalizzazione della gestione ordini e tavoli nei ristoranti, bar e locali. Consente ai clienti di consultare il menu tramite QR Code e ordinare direttamente dal proprio dispositivo, mentre il personale gestisce tavoli, comande e stato delle preparazioni tramite dashboard dedicate. L’obiettivo è ridurre errori, velocizzare il servizio e migliorare l’esperienza sia per clienti che per operatori.

---

## 2. Goals & Success Metrics

### Obiettivi Principali

- **Riduzione errori ordini:** Minimizzare errori nella presa ordini rispetto a processi manuali.
- **Efficienza operativa:** Snellire il flusso di lavoro tra sala e cucina.
- **Adozione SaaS:** Consentire la gestione multi-locale e multi-utente.
- **Esperienza cliente:** Offrire un menu digitale multilingua e un processo d’ordine semplice e veloce.

### Metriche di Successo

- **Tempo medio di creazione ordine:** < 500 ms
- **Tempo risposta menu pubblico:** < 200 ms
- **Uptime sistema:** > 99,5%
- **Numero errori ordini per 1000 ordini:** < 2
- **Numero ristoranti attivi dopo 3 mesi:** > 500
- **Numero ordini gestiti al minuto (picco):** >= 100
- **Tasso di utilizzo menu digitale da clienti:** > 80% rispetto a ordini totali

---

## 3. Target Users

### Proprietari/Manager di Locali

**Esigenze:**
- Gestione menu, tavoli e personale
- Statistiche vendite e performance
- Configurazioni fiscali e amministrative

### Camerieri

**Esigenze:**
- Apertura/chiusura tavoli
- Inserimento ordini per clienti
- Gestione richieste clienti

### Cucina

**Esigenze:**
- Visualizzazione comande in tempo reale
- Aggiornamento stato preparazione ordini

### Clienti Finali

**Esigenze:**
- Consultazione menu digitale (multilingua)
- Ordinazione semplice e veloce senza registrazione
- Possibilità di richiedere assistenza

---

## 4. Core Features

### 4.1 Gestione Locale

- **Multi-locale:** Un account può gestire più locali.
- **Dati locale:** Nome, indirizzo, logo, contatti, configurazioni fiscali.
- **Configurazione fiscale:** Raccolta dati obbligatori (P.IVA, regime fiscale, aliquote IVA, ecc.).

### 4.2 Gestione Tavoli

- **CRUD tavoli:** Creazione, modifica, eliminazione tavoli.
- **Posizionamento e stato tavolo:** Numero, posizione, stato (libero, occupato, in chiusura).
- **QR Code:** Generazione automatica QR Code per accesso menu digitale e apertura sessione tavolo.
- **Gestione sessioni tavolo:** Sessione unica per gruppo clienti, associata a tutti gli ordini fino a chiusura.

### 4.3 Menu Digitale Multilingua

- **Gestione categorie:** Antipasti, Primi, Secondi, Bevande, Dessert, ecc.
- **Gestione prodotti:** Nome, descrizione, immagine, prezzo, allergeni, disponibilità.
- **Varianti prodotto:** Opzioni obbligatorie e opzionali (es. formati, ingredienti extra) tramite gruppi di opzioni.
- **Attivazione/disattivazione prodotti**
- **Ordinamento e visibilità menu**
- **Gestione traduzioni:** Italiano, Inglese (struttura predisposta per altre lingue).

### 4.4 Ordini

- **Ordini anonimi:** Nessuna autenticazione cliente richiesta nell’MVP.
- **Flusso ordine:** Selezione prodotti → aggiunta varianti → invio ordine → stato ordine.
- **Associazione ordine:** Tavolo, sessione cliente, cameriere (se inserito da personale).
- **Stati ordine:** ricevuto, in preparazione, pronto, consegnato, chiuso, cancellato.
- **Gestione note e quantità per prodotto**
- **Storico ordini per sessione tavolo**

### 4.5 Kitchen Display System (KDS)

- **Dashboard cucina:** Visualizzazione ordini nuovi, in preparazione, completati.
- **Cambio stato ordine**
- **Filtri per categoria prodotto**
- **Notifiche real-time nuovi ordini**

### 4.6 Gestione Utenti e Ruoli

- **Ruoli:** Owner, Manager, Cameriere, Cucina
- **Permessi granulari** per ogni ruolo (gestione locale, utenti, menu, ordini, tavoli, comande).

### 4.7 Dashboard e Statistiche

- **Metriche giornaliere:** Numero ordini, incasso, tavoli occupati, prodotti più venduti.
- **Statistiche avanzate:** Vendite per periodo, prodotti più richiesti, orari di maggiore affluenza.

### 4.8 Notifiche

- **Real-time:** Nuovo ordine, ordine modificato, ordine pronto (WebSocket).
- **Email:** Report giornaliero, riepilogo vendite, alert sistema.

### 4.9 API Pubbliche e Private

- **API REST versionate** per tutte le principali funzionalità (menu, ordini, tavoli, prodotti).
- **Gestione errori standardizzata**
- **Autenticazione e autorizzazione tramite Laravel Sanctum e Policies**

---

## 5. Technical Architecture

### 5.1 Stack Tecnologico

- **Frontend:** Next.js, TypeScript, Tailwind CSS, React Query
- **Backend:** Laravel 12 (PHP 8.4), REST API, Laravel Sanctum, Laravel Reverb (WebSocket)
- **Database:** PostgreSQL
- **Cache:** Redis
- **Storage:** S3 compatibile (immagini prodotti, loghi, QR Code)
- **Real-time:** Laravel Reverb, Laravel Echo

### 5.2 Modello Dati Principale

```plaintext
users
  id, name, email, password

restaurants
  id, owner_id, name, address, logo

restaurant_users
  id, restaurant_id, user_id, role

tables
  id, restaurant_id, number, status, location, seats

table_sessions
  id, restaurant_id, table_id, session_token, status, started_at, closed_at

categories
  id, restaurant_id, name, position

category_translations
  id, category_id, locale, name

products
  id, category_id, name, description, price, image, available

product_translations
  id, product_id, locale, name, description

product_option_groups
  id, product_id, name, type, required, min_selection, max_selection

product_options
  id, option_group_id, name, price_modifier, available

orders
  id, restaurant_id, table_id, table_session_id, status, total, created_by

order_items
  id, order_id, product_id, quantity, notes

order_item_options
  id, order_item_id, option_id, price

restaurant_settings
  id, restaurant_id, vat_number, tax_code, address, city, postal_code, default_tax_rate, currency, fiscal_configuration
```

### 5.3 API Principali

- **Menu pubblico:** `GET /api/v1/restaurants/{id}/menu`, `GET /api/v1/tables/{id}/menu`
- **Ordini:** `POST /api/v1/orders`, `PUT /api/v1/orders/{id}/status`, `GET /api/v1/orders/{id}`
- **Tavoli:** `GET /api/v1/tables`, `POST /api/v1/tables`
- **Prodotti:** `GET /api/v1/products`, `POST /api/v1/products`, `PUT /api/v1/products/{id}`, `DELETE /api/v1/products/{id}`

**Gestione errori API:**  
Formato standardizzato, codici HTTP (200, 201, 400, 401, 403, 404, 422, 429, 500).

**Versionamento:**  
Tutte le API sono versionate tramite prefisso `/api/v1/`.

### 5.4 Logging, Monitoring, Backup

- **Logging:** Laravel Logging/Monolog, log JSON strutturati (eventi chiave: errori, autenticazioni, modifiche, ordini).
- **Monitoring:** APM, monitoring server, alert automatici su errori, performance, risorse.
- **Backup:** Backup automatico giornaliero database (retention 30 giorni, cifrati), backup storage con versionamento e replica geografica.

---

## 6. Non-Functional Requirements

### Performance

- **API menu:** < 200 ms
- **Creazione ordine:** < 500 ms
- **Aggiornamento stato ordine:** < 300 ms
- **Dashboard statistiche:** < 2 s
- **Scalabilità:** Supporto a 500 ristoranti, 5.000 utenti amministrativi, 20.000 clienti simultanei, 100 ordini/minuto.

### Scalabilità

- **Database:** Indici, query ottimizzate, paginazione, read replica futura.
- **Cache:** Redis per menu, sessioni, configurazioni.
- **Code asincrone:** Laravel Queue per notifiche, email, report.
- **Real-time:** WebSocket per notifiche cucina e sala.
- **Storage:** Object storage per file statici.

### Sicurezza

- **Autenticazione:** Laravel Sanctum
- **Autorizzazione:** Laravel Policies
- **Isolamento dati:** Tutte le query e API limitate per ristorante
- **Validazione input:** Server-side e client-side
- **Rate limiting:** API rate limiting per prevenire abusi
- **Backup e disaster recovery:** RPO max 24h, RTO max 4h

---

## 7. Out of Scope (v1)

- **Pagamenti digitali al tavolo** (Stripe, POS)
- **Integrazione POS e stampanti fiscali**
- **Gestione magazzino**
- **Fidelity card e programmi fedeltà**
- **Prenotazioni tavoli**
- **Login cliente e storico ordini personale**
- **App mobile dedicata**
- **Fatturazione elettronica/SDI**
- **AI per analisi vendite avanzate**
- **Stampa comande cartacea**

---

## 8. Open Questions

- **Personalizzazione grafica menu pubblico:** Quale livello di personalizzazione (colori, font, branding) sarà richiesto nell’MVP?
- **Gestione allergeni:** Quale dettaglio e struttura dati per allergeni è necessario (es. elenco standardizzato)?
- **Gestione orari di apertura/chiusura locale:** Serve già nell’MVP per mostrare menu solo in orari attivi?
- **Gestione multi-valuta:** È sufficiente una valuta per locale o serve supporto multi-valuta per menu?
- **Gestione accessibilità:** Quali requisiti di accessibilità (WCAG) sono richiesti per il menu pubblico?
- **Limiti sessione tavolo:** Serve un timeout automatico o solo chiusura manuale da parte del personale?
- **Gestione immagini:** Limiti di dimensione/risoluzione immagini prodotti e loghi?

---

**Nota:** Questo documento rappresenta la specifica dettagliata per la realizzazione dell’MVP di TableFlow. Tutte le implementazioni dovranno attenersi ai requisiti qui descritti. Eventuali modifiche o evoluzioni dovranno essere discusse e documentate in successivi aggiornamenti del PRD.