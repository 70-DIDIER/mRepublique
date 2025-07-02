CREATE TABLE utilisateurs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nom TEXT,
  date_creation TEXT
);
CREATE TABLE articles (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  titre TEXT,
  contenu TEXT,
  date_publication TEXT,
  utilisateur_id INTEGER,
  FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

