package models

import "github.com/jmoiron/sqlx"

type Category struct {
	ID   int    `db:"id"   json:"id"`
	Name string `db:"name" json:"name"`
	Slug string `db:"slug" json:"slug"`
	Type string `db:"type" json:"type"`
}

type CategoryRepository struct {
	DB *sqlx.DB
}

func (r *CategoryRepository) All(catType string) ([]Category, error) {
	cats := []Category{}
	q := `SELECT id, name, slug, type FROM cat`
	args := []any{}
	if catType != "" {
		q += " WHERE type = ?"
		args = append(args, catType)
	}
	q += " ORDER BY name ASC"
	err := r.DB.Select(&cats, r.DB.Rebind(q), args...)
	return cats, err
}

func (r *CategoryRepository) BySlug(slug string) (*Category, error) {
	cat := &Category{}
	err := r.DB.Get(cat, r.DB.Rebind(`SELECT * FROM cat WHERE slug = ? LIMIT 1`), slug)
	return cat, err
}
