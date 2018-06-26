-- dev 1.4.0
CREATE TABLE ruote(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nome VARCHAR(255),
    descrizione TEXT,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE premi(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    valuta INT,
    tipo_valuta INT,
    id_item_fk INT,

    CONSTRAINT fk_premi_id_item FOREIGN KEY (id_item_fk) REFERENCES items(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE spicchi_ruote(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_ruota_fk INT NOT NULL,
    id_premio_fk INT NOT NULL,

    CONSTRAINT fk_spicchi_ruota_fk FOREIGN KEY (id_ruota_fk) REFERENCES ruote(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_spicchi_id_premio_fk FOREIGN KEY (id_premio_fk) REFERENCES premi(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE log_ruota(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    data_spin TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_spicchio_fk INT NOT NULL,
    id_utente_fk INT NOT NULL,

    CONSTRAINT fk_log_ruota_id_spicchio_fk FOREIGN KEY (id_spicchio_fk) REFERENCES spicchi_ruote(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_log_ruota_id_utente_fk FOREIGN KEY (id_utente_fk) REFERENCES utenti(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE limiti_estrazioni(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    day_limit INT,
    week_limit INT,
    nome TEXT,
    spicchio_ruota_fk INT NOT NULL,

    CONSTRAINT fk_limiti_estrazioni_spicchi_ruota_fk FOREIGN KEY (spicchio_ruota_fk) REFERENCES spicchi_ruote(id) ON DELETE CASCADE ON UPDATE CASCADE 
);