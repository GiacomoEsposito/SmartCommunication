DROP TABLE IF EXISTS utente_gruppo;
DROP TABLE IF EXISTS allegato_circolare;
DROP TABLE IF EXISTS circolare_utente;
DROP TABLE IF EXISTS utente_compilati;
DROP TABLE IF EXISTS utente_preferiti;
DROP TABLE IF EXISTS utente_visualizzati;
DROP TABLE IF EXISTS modulo_gruppo;
DROP TABLE IF EXISTS circolare_gruppo;
DROP TABLE IF EXISTS utente;
DROP TABLE IF EXISTS gruppo;
DROP TABLE IF EXISTS modulo;
DROP TABLE IF EXISTS circolare;
DROP TABLE IF EXISTS allegato;


CREATE TABLE IF NOT EXISTS utente (
	id_utente VARCHAR(255) PRIMARY KEY,
	nome_utente VARCHAR(255) not null,
	cognome_utente VARCHAR(255) not null,
	data_nascita date not null
);

CREATE TABLE IF NOT EXISTS gruppo (
	id_gruppo int PRIMARY KEY,
	categoria_gruppo VARCHAR(255) not null,
	nome_gruppo VARCHAR(255) not null
);

CREATE TABLE IF NOT EXISTS modulo (
	id_modulo int PRIMARY KEY,
	nome_modulo VARCHAR(255) not null,
	contenuto_modulo TEXT,
	data_scadenza_modulo date not null
);

CREATE TABLE IF NOT EXISTS circolare (
	id_circolare VARCHAR(255) PRIMARY KEY,
	categoria_circolare VARCHAR(255) not null,
	data_circolare date not null
);

CREATE TABLE IF NOT EXISTS allegato (
	id_allegato INT PRIMARY KEY,
	nome_allegato VARCHAR(255) not null,
	file_allegato TEXT not null
);

CREATE TABLE IF NOT EXISTS utente_gruppo (
	id_utente VARCHAR(255)  not null,
	id_gruppo INT  not null,
	FOREIGN KEY (id_utente) REFERENCES utente(id_utente),
	FOREIGN KEY (id_gruppo) REFERENCES gruppo(id_gruppo)
);

CREATE TABLE IF NOT EXISTS utente_compilati (
	id_utente VARCHAR(255)  not null,
	id_modulo INT  NOT NULL,
	file TEXT not null,
	FOREIGN KEY (id_utente) REFERENCES utente(id_utente),
	FOREIGN KEY (id_modulo) REFERENCES modulo(id_modulo)
);

CREATE TABLE IF NOT EXISTS utente_preferiti (
	id_utente VARCHAR(255)  not null,
	id_circolare VARCHAR(255)  NOT NULL,
	FOREIGN KEY (id_utente) REFERENCES utente(id_utente),
	FOREIGN KEY (id_circolare) REFERENCES circolare(id_circolare)
);

CREATE TABLE IF NOT EXISTS utente_visualizzati (
	id_utente VARCHAR(255)  not null,
	id_circolare VARCHAR(255)  NOT NULL,
	FOREIGN KEY (id_utente) REFERENCES utente(id_utente),
	FOREIGN KEY (id_circolare) REFERENCES circolare(id_circolare)
);

CREATE TABLE IF NOT EXISTS modulo_gruppo (
	id_modulo INT  NOT NULL,
	id_gruppo INT  NOT NULL,
	FOREIGN KEY (id_modulo) REFERENCES modulo(id_modulo),
	FOREIGN KEY (id_gruppo) REFERENCES gruppo(id_gruppo)
);

CREATE TABLE IF NOT EXISTS circolare_gruppo (
	id_circolare VARCHAR(255) NOT NULL,
	id_gruppo INT NOT NULL,
	FOREIGN KEY (id_circolare) REFERENCES circolare(id_circolare),
	FOREIGN KEY (id_gruppo) REFERENCES gruppo(id_gruppo)
);

CREATE TABLE IF NOT EXISTS circolare_utente (
	id_circolare VARCHAR(255) NOT NULL,
	id_utente VARCHAR(255) not null,
	FOREIGN KEY (id_circolare) REFERENCES circolare(id_circolare),
	FOREIGN KEY (id_utente) REFERENCES utente(id_utente)
);

CREATE TABLE IF NOT EXISTS allegato_circolare (
	id_allegato INT  NOT NULL,
	id_circolare VARCHAR(255) NOT NULL,
	FOREIGN KEY (id_circolare) REFERENCES circolare(id_circolare),
	FOREIGN KEY (id_allegato) REFERENCES allegato(id_allegato)

);
