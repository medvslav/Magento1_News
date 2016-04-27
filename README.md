# Magento1_News
This is module for Magento 1.x CMS.
This module for adding news articles to the Magento stores.
Articles have categories and authors. News categories have a "tree" structure.
On the frontend, there are pages with a list of news categories, a list of articles, a list of authors, pages for one category of news, one article, one author.
On the list pages, there is pagination and a drop-down list to select the number of items on the one page.
URLs for the frontend list pages - medvslavnews2/newscategory, medvslavnews2/article, medvslavnews2/author.
You can add comments on the page of the article (only registered users can add comments). Comments are moderated, i.e. the comment has to be aproved by the site administrator for  successfully appearing on the article page (the confirmation has to be done in the Admin area -> News2 -> Manage Article Comments).
News articles will be displayed in RSS-feed site.

The articles related to the categories of news and the authors as "many-to-one".
The articles related to the products and the categories of products as "many-to-many (links to pages of the related products and related categories of products appearing on the article page, as well as links to pages of the related articles appear on the product pages and product category pages).

In the admin panel main menu, there is an item - "News2" and submenus - "Author", "Newscategory", "Article", "Manage Article Comments".
There are standard Magento pages to view, add, edit, delete articles, news categories, authors.
It is possible to bind categories of news, articles, authors to separate Store Views.

The editing article page has fields:
- Author (for binding this article to the author)
- Newscategory (for binding this article to news category)
- Title
- Description
- Content
- publication_date
- Image
- Status
- Show in rss
- Allow Comments
- Meta-title
- Meta-description
- Meta-keywords
- Store Views
- Associated products (for binding this article to the goods)
- Associated categories (for binding this article to the categories of products)

The editing news category page has fields:
- Name
- Description
- Status
- Meta-title
- Meta-description
- Meta-keywords
- Store Views
- Add Root Newscategory
- Add Child Newscategory

The editing author page has fields:
- Name
- Description
- Email
- Status
- Meta-title
- Meta-description
- Meta-keywords
- Store Views-

There is a page for management comments of articles.
It has the following fields:
- Title
- Comment
- Status
- Poster name
- Poster e-mail
- Store Views

In the admin panel, the additional tab "Articles" is added on the editing product page for binding this product to the articles.
And the additional tab "Articles" is added on the editing product category page for binding this category to the articles.

