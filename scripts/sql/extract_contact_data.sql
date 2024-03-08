SELECT 
CASE WHEN u.email IS NOT NULL THEN u.email ELSE c.email END as email,
split_part(u.name, ' ', 1) as fname, 
split_part(u.name, ' ', 2)||' '||split_part(u.name, ' ', 3) as lname, 
c.title,
'http://www.gapyear365.com/company/'||c.url_name as profile_url,
'http://admin.gapyear365.com/company/'||c.url_name||'/edit' as admin_url
from 
company c 
left join euser u on (u.company_id = c.id) 
left join comp_cat_map m on (c.id = m.company_id) 
where 
m.category_id =7 
order by c.title asc;

