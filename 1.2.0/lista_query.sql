ALTER TABLE utenti ADD COLUMN salt VARCHAR(32); -- agiunge la colonna del salt
ALTER TABLE utenti MODIFY COLUMN username VARCHAR(20); -- modifica il campo dell'username

-- Nazionalità
ALTER TABLE utenti ADD COLUMN nazione_fk CHAR(2) DEFAULT 'NN';
ALTER TABLE utenti ADD FOREIGN KEY (nazione_fk) REFERENCES nazioni(id) ON DELETE CASCADE;
UPDATE utenti SET `nazione_fk` = 'NN' WHERE (`nazione_fk` IS NULL);

-- News
CREATE TABLE notizie(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    titolo VARCHAR(255),
    testo TEXT,
    foto TEXT,
    data_creazione DATETIME,
    importante BOOLEAN DEFAULT false,
    link TEXT
);
-- seleiona le notizie importanti e le ultime con diff di 1 day
SELECT titolo, testo, foto, importante FROM notizie WHERE importante = true OR data_creazione >= (NOW() - INTERVAL 1 DAY);

-- creo tabella cheater che userò se non combaciano gli hash 
CREATE TABLE cheater(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    utente_fk INT,
    data DATETIME,
    salt_good TEXT,
    salt_bad TEXT,
    punteggio INT,
    
    CONSTRAINT fk_utente_fk FOREIGN KEY (utente_fk) REFERENCES utenti(id) ON DELETE CASCADE
);

-- ban utente
ALTER TABLE utenti ADD COLUMN banned BOOLEAN DEFAULT FALSE;

-- codici riscatto
CREATE TABLE tipo_codici(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    titolo VARCHAR(255),
    descrizione TEXT
);

CREATE TABLE codici(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    sigla VARCHAR(9),
    data_creazione DATETIME,
    scadenza DATETIME,
    tipo_codice_fk INT,
    valore INT,

    CONSTRAINT fk_tipo_codice_fk FOREIGN KEY (tipo_codice_fk) REFERENCES tipo_codici(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE codici_riscattati(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    data_riscatto DATETIME,
    id_codice_fk INT NOT NULL,
    id_utente_fk INT NOT NULL,

    CONSTRAINT fk_id_codice_fk FOREIGN KEY (id_codice_fk) REFERENCES codici(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_id_utente_fk FOREIGN KEY (id_utente_fk) REFERENCES utenti(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- crea skin
CREATE TABLE items(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nome VARCHAR(255),
    costo INT,
    codice VARCHAR(4)
);

CREATE TABLE item_acquistati(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_item_fk INT NOT NULL,
    id_utente_fk INT NOT NULL,
    data_acquisto DATETIME,

    CONSTRAINT fk_id_item_fk FOREIGN KEY (id_item_fk) REFERENCES items(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_id_utente_fk2 FOREIGN KEY (id_utente_fk) REFERENCES utenti(id) ON DELETE CASCADE ON UPDATE CASCADE
);

--aggiunta scadenza alle notizie

ALTER TABLE notizie ADD COLUMN scadenza DATETIME;
-- creare tabella tipo item
CREATE TABLE tipo_items(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nome VARCHAR(255)
);

-- aggingere colonna tipo items ad items
ALTER TABLE items ADD COLUMN tipo INT;
ALTER TABLE items ADD CONSTRAINT fk_tipofk FOREIGN KEY (tipo) REFERENCES tipo_items(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- missioni DA VEDERE
CREATE TABLE missioni(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    titolo VARCHAR(255),
    descrizione TEXT
);

CREATE TABLE missioni_completate(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    data_completamento DATETIME,
    id_missione_fk INT,
    id_utente_fk INT,

    CONSTRAINT fk_id_missione_fk FOREIGN KEY (id_missione_fk) REFERENCES missioni(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_id_utente_fk FOREIGN KEY (id_utente_fk) REFERENCES utenti(id) ON DELETE CASCADE ON UPDATE CASCADE
);

SELECT codici.scadenza, valore 
FROM codici, codici_riscattati, utenti 
WHERE utenti.username = ? AND utenti.password = ? AND codici.sigla = ? AND codici_riscattati.id_utente_fk = utenti.id AND codici_riscattati.id_codice_fk = codici.id



-- Aggiunge tutte le skin default alla tabella selected_items
 INSERT INTO selected_items
            (selected_items.player,
             selected_items.bus,
             selected_items.id_utente_fk)
SELECT A.id as player, B.id as bus, utenti.id as id_utente_fk
FROM utenti, item_acquistati A, item_acquistati B
WHERE A.id_utente_fk = utenti.id AND B.id_utente_fk = utenti.id AND A.id_item_fk = 1 AND B.id_item_fk = 9;

-- #################### 1.2.0 ##################
-- Crea tabella per le skin selezionate
CREATE TABLE selected_items(
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  player INT NOT NULL,
  bus INT NOT NULL,
  id_utente_fk INT NOT NULL,
   
  CONSTRAINT fk_selSkin_player_fk FOREIGN KEY (player) REFERENCES item_acquistati(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_selSkin_bus_fk FOREIGN KEY (bus) REFERENCES item_acquistati(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_selSkin_id_utente_fk FOREIGN KEY (id_utente_fk) REFERENCES utenti(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Crea versioni
INSERT INTO `version` ( `piattaforma`, `versione`) VALUES
('Android','1.0.4'),('Apple','1.0.4'),
('Android','1.0.5'),('Apple','1.0.5'),
('Android','1.1.0'),('Apple','1.1.0'),
('Android','1.0.6'),('Apple','1.0.6'),
('Apple','1.1.1'),('Android','1.1.2'),
('Android','1.1.3'),('Apple','1.1.2'),
('Android','1.1.4'),('Apple','1.1.5'),
('Android','1.1.5'),('Apple','1.2.0'),('Android','1.2.0');

-- query aggiungere tutte le skin bus di default
INSERT INTO item_acquistati (item_acquistati.id_item_fk, item_acquistati.id_utente_fk, item_acquistati.data_acquisto) SELECT 9 as id_item_fk, p.id as id_utente_fk, NOW() as data_acquisto FROM utenti p LEFT OUTER JOIN item_acquistati s ON s.`id_utente_fk` = p.id AND s.id_item_fk = 9 WHERE s.`id_item_fk` IS NULL; 
-- Query aggiunge tutte le skin player di default
INSERT INTO item_acquistati (item_acquistati.id_item_fk, item_acquistati.id_utente_fk, item_acquistati.data_acquisto) SELECT 1 as id_item_fk, p.id as id_utente_fk, NOW() as data_acquisto FROM utenti p LEFT OUTER JOIN item_acquistati s ON s.`id_utente_fk` = p.id AND s.id_item_fk = 1 WHERE s.`id_item_fk` IS NULL;

-- agiungi versione tabella a punteggi
ALTER TABLE punteggi ADD COLUMN version_fk INT;
ALTER TABLE punteggi ADD CONSTRAINT fk_version_fk FOREIGN KEY (version_fk) REFERENCES version(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- versione ad utenti
UPDATE utenti SET versione = NULL;
ALTER TABLE utenti MODIFY column versione INT;
ALTER TABLE utenti ADD CONSTRAINT fk_versione FOREIGN KEY (versione) REFERENCES version(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- aggiunge skin e bus selezionati alla tabella selected_items fa anche in controllo che non ci siano già nella tabella
 INSERT INTO selected_items
            (selected_items.player,
             selected_items.bus,
             selected_items.id_utente_fk)
SELECT agg.player,
       agg.bus,
       agg.id_utente_fk
FROM   (SELECT A.id      AS player,
               B.id      AS bus,
               utenti.id AS id_utente_fk
        FROM   utenti,
               item_acquistati A,
               item_acquistati B
        WHERE  A.id_utente_fk = utenti.id
               AND B.id_utente_fk = utenti.id
               AND A.id_item_fk = 1
               AND B.id_item_fk = 9) AS agg
       LEFT OUTER JOIN selected_items SE
                    ON SE.id_utente_fk = agg.id_utente_fk
WHERE  SE.id_utente_fk IS NULL  

-- query per modificare i vecchi score
UPDATE punteggi SET version_fk = (SELECT version.id FROM version, utenti WHERE version.versione = '1.0.4' AND piattaforma = 'Android' GROUP BY version.id)  WHERE punteggi.versione = '1.0.4';
UPDATE punteggi SET version_fk = (SELECT version.id FROM version, utenti WHERE version.versione = '1.0.4' AND piattaforma = 'Apple' GROUP BY version.id)  WHERE punteggi.versione = '1.0.4';
UPDATE punteggi SET version_fk = (SELECT version.id FROM version, utenti WHERE version.versione = '1.0.5' AND piattaforma = 'Android' GROUP BY version.id)  WHERE punteggi.versione = '1.0.5';
UPDATE punteggi SET version_fk = (SELECT version.id FROM version, utenti WHERE version.versione = '1.0.5' AND piattaforma = 'Apple' GROUP BY version.id)  WHERE punteggi.versione = '1.0.5';
UPDATE punteggi SET version_fk = (SELECT version.id FROM version, utenti WHERE version.versione = '1.1.0' AND piattaforma = 'Android' GROUP BY version.id)  WHERE punteggi.versione = '1.1.0';
UPDATE punteggi SET version_fk = (SELECT version.id FROM version, utenti WHERE version.versione = '1.1.0' AND piattaforma = 'Apple' GROUP BY version.id)  WHERE punteggi.versione = '1.1.0';
UPDATE punteggi SET version_fk = (SELECT version.id FROM version, utenti WHERE version.versione = '1.0.6' AND piattaforma = 'Android' GROUP BY version.id)  WHERE punteggi.versione = '1.0.6';
UPDATE punteggi SET version_fk = (SELECT version.id FROM version, utenti WHERE version.versione = '1.0.6' AND piattaforma = 'Apple' GROUP BY version.id)  WHERE punteggi.versione = '1.0.6';
UPDATE punteggi SET version_fk = (SELECT version.id FROM version, utenti WHERE version.versione = '1.1.1' AND piattaforma = 'Apple' GROUP BY version.id)  WHERE punteggi.versione = '1.1.1';
UPDATE punteggi SET version_fk = (SELECT version.id FROM version, utenti WHERE version.versione = '1.1.2' AND piattaforma = 'Android' GROUP BY version.id)  WHERE punteggi.versione = '1.1.2';
UPDATE punteggi SET version_fk = (SELECT version.id FROM version, utenti WHERE version.versione = '1.1.2' AND piattaforma = 'Apple' GROUP BY version.id)  WHERE punteggi.versione = '1.1.2';
UPDATE punteggi SET version_fk = (SELECT version.id FROM version, utenti WHERE version.versione = '1.1.3' AND piattaforma = 'Android' GROUP BY version.id)  WHERE punteggi.versione = '1.1.3';
UPDATE punteggi SET version_fk = (SELECT version.id FROM version, utenti WHERE version.versione = '1.1.4' AND piattaforma = 'Android' GROUP BY version.id)  WHERE punteggi.versione = '1.1.4';
UPDATE punteggi SET version_fk = (SELECT version.id FROM version, utenti WHERE version.versione = '1.1.5' AND piattaforma = 'Android' GROUP BY version.id)  WHERE punteggi.versione = '1.1.5';
UPDATE punteggi SET version_fk = (SELECT version.id FROM version, utenti WHERE version.versione = '1.1.5' AND piattaforma = 'Apple' GROUP BY version.id)  WHERE punteggi.versione = '1.1.5';
UPDATE punteggi SET version_fk = (SELECT version.id FROM version, utenti WHERE version.versione = '1.2.0' AND piattaforma = 'Android' GROUP BY version.id)  WHERE punteggi.versione = '1.2.0';
UPDATE punteggi SET version_fk = (SELECT version.id FROM version, utenti WHERE version.versione = '1.2.0' AND piattaforma = 'Apple' GROUP BY version.id)  WHERE punteggi.versione = '1.2.0';

-- Query per aggiungere tutti i vecchi utenti nella tabella score
INSERT INTO punteggi (punteggi.valore, punteggi.id_utenteFK, punteggi.id_seasonFK, punteggi.data, punteggi.version_fk) SELECT 0 as valore, p.id as id_utenteFK, 2 as id_seasonFK, NOW() as data, 1 as version_fk FROM utenti p LEFT OUTER JOIN punteggi s ON s.`id_utenteFK` = p.id WHERE s.`id_utenteFK` IS NULL 

-- Versione 1.2.1

-- Aggiungta versione minima al punteggio
ALTER TABLE season ADD COLUMN versione_min_android INT NOT NULL;
ALTER TABLE season ADD COLUMN versione_min_apple INT NOT NULL;
UPDATE season set versione_min_android = 1;
UPDATE season set versione_min_apple = 1;

ALTER TABLE season ADD CONSTRAINT fk_season_v_android_fk FOREIGN KEY (versione_min_android) REFERENCES version(id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE season ADD CONSTRAINT fk_season_v_apple_fk FOREIGN KEY (versione_min_apple) REFERENCES version(id) ON UPDATE CASCADE ON DELETE CASCADE;

