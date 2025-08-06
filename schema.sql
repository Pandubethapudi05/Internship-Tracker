CREATE TABLE IF NOT EXISTS Users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT,
  email TEXT UNIQUE,
  password TEXT,
  role TEXT -- 'student' or 'faculty'
);

CREATE TABLE IF NOT EXISTS Projects (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT,
  domain TEXT,
  duration TEXT,
  stipend TEXT,
  posted_by INTEGER, -- user id of faculty
  FOREIGN KEY(posted_by) REFERENCES Users(id)
);

CREATE TABLE IF NOT EXISTS Applications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER,
  project_id INTEGER,
  status TEXT, -- 'Applied', 'Shortlisted', 'Selected', 'Rejected'
  FOREIGN KEY(user_id) REFERENCES Users(id),
  FOREIGN KEY(project_id) REFERENCES Projects(id)
);