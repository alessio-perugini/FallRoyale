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

INSERT INTO `items` (`id`, `nome`, `costo`, `codice`) VALUES
(1, 'Explorer', 0, 'EXPR'),
(2, 'Scarlet girl', 1000, 'SCRG'),
(3, 'Assault trooper', 2000, 'ASST'),
(4, 'Crimson warrior', 3000, 'CRMW'),
(5, 'Black vanguard', 7000, 'BLKV'),
(6, 'Leviathan', 7000, 'LVTH'),
(7, 'Raptor', 8000, 'RPTR'),
(8, 'Cuddle leader', 9999, 'CDDL');

ALTER TABLE utenti DROP punteggio;