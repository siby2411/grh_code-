CREATE TABLE IF NOT EXISTS avis_anonymes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note INT NOT NULL,
    org_interne TEXT,
    points_amelioration TEXT,
    climat_social TEXT,
    coaching TEXT,
    commentaires TEXT,
    date_soumission DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
