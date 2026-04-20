package models

import (

	"github.com/jmoiron/sqlx"
)

type Post struct {
	ID      int       `db:"id"         json:"id"`
	Title   string    `db:"title"      json:"title"`
	Content string    `db:"content"    json:"content,omitempty"`
	Author  string    `db:"author"     json:"author"`
	Status  string    `db:"status"     json:"status"`
	Type    string    `db:"type"       json:"type"`
	Date    string    `db:"date"       json:"date"`
	Slug    string    `db:"slug"       json:"slug"`
	Cat     *int      `db:"cat"        json:"category_id"`
}

type PostParam struct {
	ID     int    `db:"id"      json:"id"`
	PostID int    `db:"post_id" json:"post_id"`
	Name   string `db:"name"    json:"name"`
	Value  string `db:"value"   json:"value"`
	Type   string `db:"type"    json:"type"`
}

type PostRepository struct {
	DB      *sqlx.DB
	SiteURL string
}

func (r *PostRepository) Find(filters map[string]string, limit, offset int) ([]Post, int, error) {
	posts := []Post{}
	var total int

	query := `SELECT id, title, author, status, type, date, slug, cat FROM posts WHERE 1=1`
	countQ := `SELECT COUNT(*) FROM posts WHERE 1=1`
	args := []any{}

	if v, ok := filters["type"]; ok && v != "" {
		query += " AND type = ?"
		countQ += " AND type = ?"
		args = append(args, v)
	}
	if v, ok := filters["status"]; ok && v != "" {
		query += " AND status = ?"
		countQ += " AND status = ?"
		args = append(args, v)
	}
	if v, ok := filters["cat"]; ok && v != "0" && v != "" {
		query += " AND cat = ?"
		countQ += " AND cat = ?"
		args = append(args, v)
	}
	if v, ok := filters["q"]; ok && v != "" {
		v = "%" + v + "%"
		query += " AND (title LIKE ? OR content LIKE ?)"
		countQ += " AND (title LIKE ? OR content LIKE ?)"
		args = append(args, v, v)
	}

	_ = r.DB.Get(&total, r.DB.Rebind(countQ), args...)
	
	finalArgs := append(args, limit, offset)
	err := r.DB.Select(&posts, r.DB.Rebind(query+" ORDER BY date DESC LIMIT ? OFFSET ?"), finalArgs...)
	return posts, total, err
}

func (r *PostRepository) All(postType string, limit, offset int) ([]Post, int, error) {
	return r.Find(map[string]string{"type": postType, "status": "1"}, limit, offset)
}

func (r *PostRepository) BySlug(slug string) (*Post, error) {
	post := &Post{}
	err := r.DB.Get(post, r.DB.Rebind(`SELECT * FROM posts WHERE slug = ? AND status = '1' LIMIT 1`), slug)
	return post, err
}

func (r *PostRepository) ByID(id int) (*Post, error) {
	post := &Post{}
	err := r.DB.Get(post, r.DB.Rebind(`SELECT * FROM posts WHERE id = ? LIMIT 1`), id)
	return post, err
}

func (r *PostRepository) Params(postID int) ([]PostParam, error) {
	params := []PostParam{}
	err := r.DB.Select(&params, r.DB.Rebind(`SELECT * FROM post_params WHERE post_id = ?`), postID)
	return params, err
}
