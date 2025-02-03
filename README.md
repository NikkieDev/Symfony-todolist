This is a simple API for a todo-list application, written in Symfony as a learning experience.

------------------------- ---------- -------- ------ ------------------- 
  Name                      Method     Scheme   Host   Path               
 ------------------------- ---------- -------- ------ ------------------- 
  create_item               POST       ANY      ANY    /item/create       
  change_item_status        PUT|POST   ANY      ANY    /item/status/{id}  
  change_item_description   PUT        ANY      ANY    /item/description  [Not implemented]
  delete_item               DELETE     ANY      ANY    /item/delete       [Not Implemented]
  app_random_word           ANY        ANY      ANY    /random/word       [Easter egg]
  recent_lists              GET        ANY      ANY    /lists
  create_list               POST       ANY      ANY    /list/create
  rename_list               PUT        ANY      ANY    /list/name/{id}
  delete_list               DELETE     ANY      ANY    /list/delete/{id}
 ------------------------- ---------- -------- ------ -------------------
