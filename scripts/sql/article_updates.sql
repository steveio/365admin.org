
INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Acting / Drama / Film Summer Camp',
'Acting, drama and film programs are offered at specialist film schools and traditional summer camps.  They are great for those considering careers in movie / TV production or to get a taste of working in the entertainment industry.   Typically performing arts programs enable kids to gain experience acting in front of camera or as a part of a stage based theatrical production.  Other fun skills to learn include make-up artist or costume/wardrobe designer.  Film and TV school programs also teach career skills in modern production using professional studio equipment including camera / photographic work, sound recording, lighting or editing , post-production and special effects.',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/acting-drama-film-summer-camps'
);

INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Adventure Summer Camp',
'Summer adventure camps usually include a range of fun and activity based outdoors experiences - kids and teens can learn exploration skills, go wilderness trekking, camping, conquer assault courses, zip wires, learn horse riding, river rafting, sailing or a range of water sports.',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/adventure-summer-camps'
);

INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Art Summer Camp',
'Art camps are for the creative and allow you to have fun while learning and improving your skills from established artists who tutor skills in painting, ceramics, sculpture and craft activities.',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/art-summer-camps'
);

INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Baseball Summer Camp',
'Improve your swing, become a legendary pitcher or help your team win the series at a baseball camp!  Baseball and softball camps are hugely popular with sports enthusiasts across America.  Parents should research and choose overnight and stay away summer camp baseball programs carefully as coaching is available for players of all levels and abilities; from world class major league players offering advanced players professional guidance to take their game to the next level to college athletes teaching the basics in a fun and casual team building environment.',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/baseball-summer-camps'
);

INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Basketball Summer Camp',
'Basketball summer camps for kids and teens, boys and girls of all levels help improve on court skills, athletic ability and teach kids self esteem and how to become a team player.  Some camps offer dedicated expert training from experienced NBA stars while many sports camps feature basketball as a part of a varied sports activity schedule.   Sleep away basketball programs at summer camps or school campuses are available throughout the USA and sessions are usually for between 1 and 4 weeks.',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/basketball-summer-camps'
);

INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Cheerleading Summer Camp',
'Spend a summer training with top cheer and tumbling coaches on a dedicated cheer leading program or take a course as a part of a general summer camp dance experience.',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/cheerleading-summer-camps'
);

INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Computing Summer Camp',
'Computer science and coding camps will look great on any CV and are great places for tomorrows software engineers to learn new skills and network with peers and industry gurus.  Students can master programming languages including Java and C++, learn app design, to develop or design websites or study areas such as alogrithms, artificial intelligence or robotics.', 
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/computing-summer-camps'
);

INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Cooking Summer Camp',
'Summer cooking camps for kids and teens teach the basics of cooking.  Learn how to use different ingredients, master baking and cake making and impress your friends by preparing a range dishes from around the world.  Whether you are planning a career in the kitchen or just interested in improving your skills a summer cookery camp is a rewarding and fun experience for those who love food.',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/cooking-summer-camps'
);

INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Dance Summer Camp',
'Dance summer camps are widely available in US, UK and worldwide offering a range of summer dance training programs across a range of styles with professional teachers and choreographers.  Whether your favourite dance style is Hip Hop, Jazz, Modern, Ballet or Tap a sumer dance training camp is the place to perfect your moves!',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/dance-summer-camps'
);

INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Golf Summer Camp',
'Overnight and day junior golf camps in USA, UK and Europe offer kids and teens golf training programs.  At golf summer camp boys and girls can improve golf skills for match and tournament play from established masters or just learn golf basics of driving, chipping and putting.',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/golf-summer-camps'
);


INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Gymnastics Summer Camp',
'Gymnastics summer camps for beginners and experienced gymnasts of all ages and levels offer coaching and training to help your child improve and learn new gym skills.  Elite gymnasts can train at day camps with world champions and Olympians while commercial gymnastics overnight sports summer camps offer gymastics disciplines along with a range of other activities giving a real summer camp experience.',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/gymnastics-summer-camps'
);


INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Horse Riding Summer Camp',
'Summer horse riding or equestrian camps in US and Europe offer programs for kids between 7 and 17.  Programs in horseback riding skills are available for begginers or seasoned riders and are also enjoyed by boys and girls with an interest in learning horse and ponie care or stablecraft.  Tuition at summer camp is provided by experienced riders who ensure a safe experience.  Horseback riding is often available on wilderness and outdoor adventure camps as well as at specialist centres where eventing, dressage, jumping and polo can be learned.',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/horse-riding-summer-camps'
);

INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Kayaking / Canoeing Summer Camp',
'Kayaking and canoeing sessions led by qualified instructors are available at adventure summer camps offering a range of water sports activities.  You can also find canoe activites offered at dedicated whitewater river rafting centres and as a part of some wilderness summer camp programs.  Popular at locations in the US, Canada and UK, kids will love paddling at summer camp this summer!',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/kayaking-summer-camps'
);

INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Nature Summer Camp',
'Nature summer camps offer kids learning experiences with highly trained instructors and guides who teach about animals, plants, birds and fish.  Programs also cover survival skills, tracking, exploration and expedition training teaching valuable lifeskills.',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/nature-summer-camps'
);


INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Weight Loss Summer Camp',
'At weight loss summer camps in US and Europe kids, teens and adults learn to live healthy, how to diet, lose weight, exercise and gain self esteem.  Summer weight loss programs are available across America and worldwide and offer a range of healthy lifestyle activites along with menu planning and cookery classes.  Weight loss camps are typically residential and programs typically last from 4 - 6 weeks.',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/weight-loss-summer-camps'
);


INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Wilderness Summer Camp',
'Wilderness summer camps teach kids outdoors and survival skills and offer adventure programs including kayaking, canoeing, hiking, climbing,  expedition and suvival skills.  Wilderness camps are often provided at mountain camp centres, in forests, by lakes or in national parks.',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/wilderness-summer-camps'
);

INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Music Summer Camp',
'On a music summer camp kids, high school students and teens practise writing, recording and performing music.  Musicians, bands, orchestra players, instrumentalists and singers will develop their musical and performance abilities which can boost college admission chances.  Popular in US, UK and Europe music summer programs are offered by Music Colleges, Universities and at creative arts summer camps.  Intensive residential programs will include daily coaching, rehearsals, master classes and performances.  Summer camp music programs cover a wide range of musical styles from classical music to urban hip hop, rock guitar bands to electronic music to virtuoso instrumental players. Programs are also available covering sound recording and studio techniques.',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/music-summer-camps'
);

INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Photography Summer Camp',
'Photography summer camps for kids and teens teach digital, 35mm and wide-format camera techniques, darkroom skills and use of modern tools like photoshop.  Summer camp photography programs are a great choice for budding photojournalists, fashion photographers, sports shooters and there are also programs hosted in beautiful landscapes where nature photographers can learn about getting that great shot outdoors.  Master key photographic skills like aperture, shutter control and composition.  Learn about different lens types, lighting and experiment with colour treatments and effects.  Photography / film students and those who enjoy taking photos alike will benefit from summer camp training with professional photographers and experienced program directors.',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/photography-summer-camps'
);


INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'), 
'Rock Climbing / Mountaineering Summer Camp',
'Rock climbing and abseiling is a popular activity taught at adventure summer camps.  Programs are also offered at specialist climbing centres where expert rope skills and mountain safety are taught by full time professional climbers and guides.  Climbing skills help young people build self confidence, learn trust and to practise balance and summer camp climbing programs are available for all levels from first time climbers to advanced teens.',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/rock-climbing-summer-camps'
);

INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Sailing Summer Camp',
'A sailing summer camp is a great choice for young skippers and often includes a range of water based activities include sailing lessons, scuba diving and marine biology programs.  Sailing programs are available at many adventure camps and also at yacht and marine clubs.   Campers on a sailing camp can learn water safety, rigging, tacking, jibing, navigation and basic rescue procedures from qualified marine instructors.  Make new friends and earn marine and yachting certifications at a sailing summer camp.',
1,
now()::timestamp
);

INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/sailing-summer-camps'
);

INSERT INTO article (
id,
title,
short_desc,
created_by,
created_date
) VALUES (
nextval('article_seq'),
'Science Summer Camp',
'Science summer camp programs are offered by academic institutions and teach a range of science and engineering disciples including biology, chemistry, geology, physics, astronomy, maths and robotics.  Science camps for kids the opportunity to become junior scientists, to practise lab experiments, learn about great scientists and their discoveries and to develop a passion for science.  For advanced teens seeking college majors there are a range of excellent programs available which include scientific theory, hands on practical science and field trips.   Scholarships are also available for gifted students.',
1,
now()::timestamp
);


INSERT INTO article_map (
article_id,
website_id,
section_uri
) VALUES (
(SELECT max(id) FROM article),
3,
'/summer-camp/programs/science-summer-camps'
);
