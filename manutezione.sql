-- CONTROLLA UTENTI MULTIPLI
SELECT utenti.*
FROM (SELECT id, username, COUNT(*) FROM `utenti` GROUP BY username HAVING COUNT(*) > 1 ORDER BY COUNT(*)) as a, utenti
where a.username = utenti.username

-- CONTROLLA ITEM ACQUISTATI UGUALi
SELECT item_acquistati.*
FROM (SELECT `id_utente_fk`, `id_item_fk`, COUNT(id_item_fk) FROM item_acquistati GROUP BY id_utente_fk, `id_item_fk` HAVING COUNT(id_item_fk) > 1 ORDER BY COUNT(`id_item_fk`)) as a, item_acquistati
WHERE item_acquistati.`id_utente_fk` = a.id_utente_fk

(SELECT `id_utenteFK`, `id`, COUNT(`id_seasonFK`) FROM punteggi GROUP BY `id_utenteFK`, `id_seasonFK` HAVING COUNT(`id_seasonFK`) > 1 ORDER BY COUNT(`id_utenteFK`))


