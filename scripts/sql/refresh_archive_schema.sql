

drop table company_archive;

create table company_archive as select * from company;

truncate table company_archive;

GRANT all ON company_archive TO oneworld365_pgsql;



drop table profile_hdr_archive;

create table profile_hdr_archive as select * from profile_hdr;

truncate table profile_hdr_archive;

GRANT all ON profile_hdr_archive TO oneworld365_pgsql;


drop table profile_courses_archive;

create table profile_courses_archive as select * from profile_courses;

truncate table profile_courses_archive;

GRANT all ON profile_courses_archive TO oneworld365_pgsql;


drop table profile_general_archive;

create table profile_general_archive as select * from profile_general;

truncate table profile_general_archive;

GRANT all ON profile_general_archive TO oneworld365_pgsql;


drop table profile_job_archive;

create table profile_job_archive as select * from profile_job;

truncate table profile_job_archive;

GRANT all ON profile_job_archive TO oneworld365_pgsql;


drop table profile_seasonaljobs_archive;

create table profile_seasonaljobs_archive as select * from profile_seasonaljobs;

truncate table profile_seasonaljobs_archive;

GRANT all ON profile_seasonaljobs_archive TO oneworld365_pgsql;


drop table profile_summercamp_archive;

create table profile_summercamp_archive as select * from profile_summercamp;

truncate table profile_summercamp_archive;

GRANT all ON profile_summercamp_archive TO oneworld365_pgsql;


drop table profile_teaching_archive;

create table profile_teaching_archive as select * from profile_teaching;

truncate table profile_teaching_archive;

GRANT all ON profile_teaching_archive TO oneworld365_pgsql;


drop table profile_tour_archive;

create table profile_tour_archive as select * from profile_tour;

truncate table profile_tour_archive;

GRANT all ON profile_tour_archive TO oneworld365_pgsql;


drop table profile_volunteer_project_archive;

create table profile_volunteer_project_archive as select * from profile_volunteer_project;

truncate table profile_volunteer_project_archive;

GRANT all ON profile_volunteer_project_archive TO oneworld365_pgsql;
