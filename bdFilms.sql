DROP DATABASE IF EXISTS bdFilms;
CREATE DATABASE IF NOT EXISTS bdFilms;
USE bdFilms;

CREATE TABLE IF NOT EXISTS Utilisateur(
   idUtilisateur INT(3) AUTO_INCREMENT,
   nom VARCHAR(25),
   prenom VARCHAR(25),
   pseudo VARCHAR(25) NOT NULL,
   email VARCHAR(30) NOT NULL,
   motDePasse VARCHAR(200) NOT NULL,
   estAdmin BOOL NOT NULL,
   PRIMARY KEY(idUtilisateur)
);

CREATE TABLE IF NOT EXISTS Acteur(
   idActeur INT(3) AUTO_INCREMENT,
   nom VARCHAR(25) NOT NULL,
   prenom VARCHAR(25),
   dateNaissance DATE NOT NULL,
   nationalite VARCHAR(20),
   PRIMARY KEY(idActeur)
);

CREATE TABLE IF NOT EXISTS Ceremonie(
   codeCere INT(3) AUTO_INCREMENT,
   dateCeremonie INT,
   nomFestival VARCHAR(30),
   PRIMARY KEY(codeCere)
);

CREATE TABLE IF NOT EXISTS Genre(
   idGenre INT(2) AUTO_INCREMENT,
   libelle VARCHAR(20) NOT NULL, /* nom du genre */
   descri VARCHAR(250),
   PRIMARY KEY(idGenre),
   UNIQUE(libelle)
);

CREATE TABLE IF NOT EXISTS Realisateur(
   idReal INT(3) AUTO_INCREMENT,
   nom VARCHAR(25) NOT NULL,
   prenom VARCHAR(25),
   dateNaissance DATE NOT NULL,
   nationalite VARCHAR(20),
   PRIMARY KEY(idReal)
);

CREATE TABLE IF NOT EXISTS Film(
   idFilm INT(3) AUTO_INCREMENT,
   titre VARCHAR(60) NOT NULL,
   descri VARCHAR(100000),
   duree INT(3),
   dateSortie DATE,
   coutTotal DECIMAL(8,2),
   boxOffice DECIMAL(8,2),
   langueVO VARCHAR(50),
   image VARCHAR(50),
   trailer VARCHAR(50),
   idReal INT(3) NOT NULL,
   PRIMARY KEY(idFilm),
   FOREIGN KEY(idReal) REFERENCES Realisateur(idReal)
);

CREATE TABLE IF NOT EXISTS Avis(
   numAvis INT(3) AUTO_INCREMENT,
   note DECIMAL(2,1),
   commentaire VARCHAR(1000),
   datePublication DATE,
   idUtilisateur INT(3) NOT NULL,
   idFilm INT(3) NOT NULL,
   PRIMARY KEY(numAvis),
   FOREIGN KEY(idUtilisateur) REFERENCES Utilisateur(idUtilisateur),
   FOREIGN KEY(idFilm) REFERENCES Film(idFilm)
);

CREATE TABLE IF NOT EXISTS Jouer(
   idFilm INT(3),
   idActeur INT(3),
   roleJoue VARCHAR(25),
   PRIMARY KEY(idFilm, idActeur),
   FOREIGN KEY(idFilm) REFERENCES Film(idFilm),
   FOREIGN KEY(idActeur) REFERENCES Acteur(idActeur)
);

CREATE TABLE IF NOT EXISTS DecernerPrixFilm(
   idFilm INT(3),
   codeCere INT(3),
   prixFilm VARCHAR(25),
   PRIMARY KEY(idFilm, codeCere),
   FOREIGN KEY(idFilm) REFERENCES Film(idFilm),
   FOREIGN KEY(codeCere) REFERENCES Ceremonie(codeCere)
);

CREATE TABLE IF NOT EXISTS AppartenirGenre(
   idFilm INT(3),
   idGenre INT(2),
   PRIMARY KEY(idFilm, idGenre),
   FOREIGN KEY(idFilm) REFERENCES Film(idFilm),
   FOREIGN KEY(idGenre) REFERENCES Genre(idGenre)
);

CREATE TABLE IF NOT EXISTS DecernerPrixActeur(
   idActeur INT(3),
   codeCere INT(3),
   prix VARCHAR(50),
   idFilm INT(3) NOT NULL,
   PRIMARY KEY(idActeur, codeCere),
   FOREIGN KEY(idActeur) REFERENCES Acteur(idActeur),
   FOREIGN KEY(codeCere) REFERENCES Ceremonie(codeCere),
   FOREIGN KEY(idFilm) REFERENCES Film(idFilm)
);

CREATE TABLE IF NOT EXISTS Watchlist(
	idUtilisateur INT(3),
    idFilm INT(3),
    dateAjout DATE,
    PRIMARY KEY(idUtilisateur, idFilm),
    FOREIGN KEY(idUtilisateur) REFERENCES Utilisateur(idUtilisateur),
    FOREIGN KEY(idFilm) REFERENCES Film(idFilm)
);

INSERT INTO Film(titre, descri, duree, dateSortie, coutTotal, boxOffice, langueVO, image, trailer, idReal) VALUES

-- Liste 2022

('Scream', 'Vingt-cinq ans après que la paisible ville de Woodsboro a été frappée par une série de meurtres violents, un nouveau tueur revêt le masque de Ghostface et prend pour cible un groupe d\'adolescents. Il est déterminé à faire ressurgir les sombres secrets du passé.', 114, '2022-01-12', 24, 138, 'Anglais', 'scream.jpg', 'https://www.youtube.com/watch?v=1QvVPKp3N00', 1),
('Uncharted', 'Nathan Drake, voleur astucieux et intrépide, est recruté par le chasseur de trésors chevronné Victor « Sully » Sullivan pour retrouver la fortune de Ferdinand Magellan, disparue il y a 500 ans. Ce qui ressemble d’abord à un simple casse devient finalement une course effrénée autour du globe pour s’emparer du trésor avant l’impitoyable Moncada, qui est persuadé que sa famille est l’héritière légitime de cette fortune. Si Nathan et Sully réussissent à déchiffrer les indices et résoudre l’un des plus anciens mystères du monde, ils pourraient rafler la somme de 5 milliards de dollars et peut-être même retrouver le frère de Nathan, disparu depuis longtemps… mais encore faudrait-il qu’ils apprennent à travailler ensemble.', 116, '2022-02-22', 120, 400, 'Anglais', 'uncharted.jpg', 'https://youtu.be/qvfQI2dB8pM', 3),
('The Batman', 'Deux années à arpenter les rues en tant que Batman et à insuffler la peur chez les criminels ont mené Bruce Wayne au coeur des ténèbres de Gotham City. Avec seulement quelques alliés de confiance - Alfred Pennyworth, le lieutenant James Gordon - parmi le réseau corrompu de fonctionnaires et de personnalités de la ville, le justicier solitaire s\'est imposé comme la seule incarnation de la vengeance parmi ses concitoyens. Lorsqu\'un tueur s\'en prend à l\'élite de Gotham par une série de machinations sadiques, une piste d\'indices cryptiques envoie le plus grand détective du monde sur une enquête dans la pègre, où il rencontre des personnages tels que Selina Kyle, alias Catwoman, Oswald Cobblepot, alias le Pingouin, Carmine Falcone et Edward Nashton, alias l\’Homme-Mystère. Alors que les preuves s’accumulent et que l\'ampleur des plans du coupable devient clair, Batman doit forger de nouvelles relations, démasquer le coupable et rétablir un semblant de justice au milieu de l’abus de pouvoir et de corruption sévissant à Gotham City depuis longtemps..', 175, '2022-03-02', 185, 770, 'Anglais', 'batman.jpg', 'https://www.youtube.com/watch?v=hGQo1axtj60', 4),
('Morbius', 'Découvrez pour la première fois au cinéma, le Docteur Michael Morbius (incarné par l’acteur oscarisé Jared Leto), anti héros énigmatique et l’un des personnages les plus captivants et torturés des personnages de Marvel dans l’univers Sony Pictures.Gravement atteint d’une rare maladie sanguine, et déterminé à sauver toutes les victimes de cette pathologie, le Dr Morbius tente un pari désespéré. Alors que son expérience semble être un succès, le remède déclenche un effet sinistre. Le bien vaincra-t-il le mal – ou Morbius succombera-t-il à ses nouvelles pulsions ?.', 108, '2022-03-30', 75, 467, 'Anglais', 'MORBIUS.jpg', 'https://youtu.be/wvAZybYcWu8', 1),
('Doctor Strange in the Multiverse of Madness', 'Dans ce nouveau film Marvel Studios, l’univers cinématographique Marvel déverrouille et repousse les limites du multivers encore plus loin. Voyagez dans l’inconnu avec Doctor Strange, qui avec l’aide d’anciens et de nouveaux alliés mystiques, traverse les réalités hallucinantes et dangereuses du multivers pour affronter un nouvel adversaire mystérieux.', 126, '2022-05-04', 200, 995, 'Anglais', 'doctorStrange.jpg', 'https://www.youtube.com/watch?v=J7u1bDo_4sk', 1),
('Top Gun: Maverick', 'Après avoir été l’un des meilleurs pilotes de chasse de la Marine américaine pendant plus de trente ans, Pete “Maverick" Mitchell continue à repousser ses limites en tant que pilote d\'essai. Il refuse de monter en grade, car cela l’obligerait à renoncer à voler. Il est chargé de former un détachement de jeunes diplômés de l’école Top Gun pour une mission spéciale qu\'aucun pilote n\'aurait jamais imaginée. Lors de cette mission, Maverick rencontre le lieutenant Bradley "Rooster" Bradshaw, le fils de son défunt ami, le navigateur Nick “Goose” Bradshaw. Face à un avenir incertain, hanté par ses fantômes, Maverick va devoir affronter ses pires cauchemars au cours d’une mission qui exigera les plus grands des sacrifices.', 130, '2022-05-25', 170, 1500, 'Anglais', 'topGun.jpg', 'https://www.youtube.com/watch?v=JYaFU81-t6c', 1),
('Jurassic World: Le Monde d\'après', 'Quatre ans après la destruction de Isla Nublar. Les dinosaures font désormais partie du quotidien de l’humanité entière. Un équilibre fragile qui va remettre en question la domination de l’espèce humaine maintenant qu’elle doit partager son espace avec les créatures les plus féroces que l’histoire ait jamais connues.', 147, '2022-06-08', 185, 1000, 'Anglais', 'jurassicWorld.jpg', 'https://www.youtube.com/watch?v=8UZ6NOLR9sQ', 1),
('Les Minions 2 : Il était une fois Gru', 'Alors que les années 70 battent leur plein, Gru qui grandit en banlieue au milieu des jeans à pattes d’éléphants et des chevelures en fleur, met sur pied un plan machiavélique à souhait pour réussir à intégrer un groupe célèbre de super méchants, connu sous le nom de Vicious 6, dont il est le plus grand fan. Il est secondé dans sa tâche par les Minions, ses petits compagnons aussi turbulents que fidèles. Avec l’aide de Kevin, Stuart, Bob et Otto – un nouveau Minion arborant un magnifique appareil dentaire et un besoin désespéré de plaire - ils vont déployer ensemble des trésors d’ingéniosité afin de construire leur premier repaire, expérimenter leurs premières armes, et lancer leur première mission.Lorsque les Vicious 6 limogent leur chef, le légendaire " Wild Knuckles ", Gru passe l’audition pour intégrer l’équipe. Le moins qu’on puisse dire c’est que l’entrevue tourne mal, et soudain court quand Gru leur démontre sa supériorité et se retrouve soudain leur ennemi juré. Contraint de s’enfuir, il n’aura d’autre choix que de se tourner vers " Wild Knuckles " lui-même, afin de trouver une solution, rencontre qui lui permettra de découvrir que même les super méchants ont parfois besoin d’amis.', 88, '2022-07-06', 80, 940, 'Anglais', 'minion.jpg', 'https://www.youtube.com/watch?v=tSFef9eeDEA', 1),
('Thor: Love and Thunder', 'Alors que Thor est en pleine introspection et en quête de sérénité, sa retraite est interrompue par un tueur galactique connu sous le nom de Gorr, qui s’est donné pour mission d’exterminer tous les dieux. Pour affronter cette menace, Thor demande l’aide de Valkyrie, de Korg et de son ex-petite amie Jane Foster, qui, à sa grande surprise, manie inexplicablement son puissant marteau, le Mjolnir. Ensemble, ils se lancent dans une dangereuse aventure cosmique pour comprendre les motivations qui poussent Gorr à la vengeance et l’arrêter avant qu’il ne soit trop tard.', 119, '2022-07-13', 250, 760, 'Anglais', 'thor.jpg', 'https://www.youtube.com/watch?v=wPPim0we5m8', 1),
('Black Panther: Wakanda Forever', 'La Reine Ramonda, Shuri, M’Baku, Okoye et les Dora Milaje luttent pour protéger leur nation des ingérences d’autres puissances mondiales après la mort du roi T’Challa. Alors que le peuple s’efforce d’aller de l’avant, nos héros vont devoir s’unir et compter sur l’aide de la mercenaire Nakia et d’Everett Ross pour faire entrer le royaume du Wakanda dans une nouvelle ère. Mais une terrible menace surgit d’un royaume caché au plus profond des océans : Talokan.', 161, '2022-11-09', 250, 859, 'Anglais', 'blackPanther.jpg', 'https://www.youtube.com/watch?v=DlGIWM_e9vg', 1),
('Avatar : la voie de l\'eau', 'Se déroulant plus d’une décennie après les événements relatés dans le premier film, AVATAR : LA VOIE DE L’EAU raconte l\'histoire des membres de la famille Sully (Jake, Neytiri et leurs enfants), les épreuves auxquelles ils sont confrontés, les chemins qu’ils doivent emprunter pour se protéger les uns les autres, les batailles qu’ils doivent mener pour rester en vie et les tragédies qu\'ils endurent..', 192, '2022-12-14', 350, 2300, 'Anglais', 'avatar.jpg', 'https://www.youtube.com/watch?v=2UEkizpGKDU', 1),
('Nope','En Californie, le décès dans des circonstances étranges de leur père amène un frère et une sœur à enquêter sur les causes. Le résultat est effrayant.',131,'2022-08-10',68,171,'Anglais','nope.jpg','https://www.youtube.com/watch?v=YjaWZtva9U8',15),
('The Flash','Les réalités s’affrontent dans THE FLASH lorsque Barry se sert de ses super-pouvoirs pour remonter le temps et modifier son passé. Mais ses efforts pour sauver sa famille ne sont pas sans conséquences sur l’avenir, et Barry se retrouve pris au piège d’une réalité où le général Zod est de retour, menaçant d’anéantir la planète, et où les super-héros ont disparu. À moins que Barry ne réussisse à tirer de sa retraite un Batman bien changé et à venir en aide à un Kryptonien incarcéré, qui n’est pas forcément celui qu’il recherche. Barry s’engage alors dans une terrible course contre la montre pour protéger le monde dans lequel il est et retrouver le futur qu’il connaît. Mais son sacrifice ultime suffira-t-il à sauver l’univers ?',144,'2023-06-14',200,270,'Anglais','flash.jpg','https://www.youtube.com/watch?v=Kx-XVtH3HsE',16),
('Black Adam','Dans l’antique Kahndaq, l’esclave Teth Adam avait reçu les super-pouvoirs des dieux. Mais il en a fait usage pour se venger et a fini en prison. Cinq millénaires plus tard, alors qu’il a été libéré, il fait régner sa conception très sombre de la justice dans le monde. Refusant de se rendre, Teth Adam doit affronter une bande de héros d’aujourd’hui qui composent la Justice Society – Hawkman, le Dr Fate, Atom Smasher et Cyclone – qui comptent bien le renvoyer en prison pour l’éternité.',124,'2022-10-19',200,390,'Anglais','blackAdam.jpg','https://www.youtube.com/watch?v=vUnRitCWjqc',17),
('Aquaman et le Royaume perdu','Black Manta, toujours hanté par le désir de venger son père, est maintenant plus puissant que jamais avec le légendaire Trident Noir entre ses mains. Pour l’anéantir, Aquaman doit s’associer à son frère Orm ancien roi d’Atlantide et actuellement emprisonné. Ensemble, ils devront surmonter leurs différences pour protéger leur royaume et sauver le monde d’une destruction irréversible.',130,'2023-12-20',160,439,'Anglais','aquaman.jpg','https://www.youtube.com/watch?v=rf_d8jA89_o',18),
('Le Chat Potté 2 : La Dernière Quête','Le Chat Potté découvre que sa passion pour l\'aventure et son mépris du danger ont fini par lui coûter cher : il a épuisé huit de ses neuf vies, et en a perdu le compte au passage. Afin de retomber sur ses pattes notre héros velu se lance littéralement dans la quête de sa vie. Il s\'embarque dans une aventure épique aux confins de la Forêt Sombre afin de dénicher la mythique Etoile à vœu, seule susceptible de lui rendre ses vies perdues. Mais quand il ne vous en reste qu\'une, il faut savoir faire profil bas, se montrer prudent et demander de l\'aide. C’est ainsi qu\'il se tourne vers son ancienne partenaire et meilleure ennemie de toujours : l\'ensorcelante Kitty Pattes De Velours. Le Chat Potté et la belle Kitty vont être aidés dans leur quête, à leur corps bien défendant, par Perro, un corniaud errant et galleux à la langue bien pendue ,et d\'une inaltérable bonne humeur. Ensemble ils tenteront de garder une longueur d\'avance sur la redoutable Boucles D\'Or et son gang des Trois Ours, véritable famille de mafieux, mais aussi sur Little Jack Horner devenu bien grand, ou encore sur le chasseur de primes le plus féroce du coin : Le Loup.', 102, '2022-12-07', 90, 470, 'Anglais', 'chat potté.jpg', 'https://www.youtube.com/watch?v=fwiLlB-hIFQ', 19),
('Buzz l’Éclair', 'La véritable histoire du légendaire Ranger de l''espace qui, depuis, a inspiré le jouet que nous connaissons tous. Après s''être échoué avec sa commandante et son équipage sur une planète hostile située à 4,2 millions d’années-lumière de la Terre, Buzz l’Éclair tente de ramener tout ce petit monde sain et sauf à la maison. Pour cela, il peut compter sur le soutien d’un groupe de jeunes recrues ambitieuses et sur son adorable chat robot, Sox. Mais l''arrivée du terrible Zurg et de son armée de robots impitoyables ne va pas leur faciliter la tâche, d’autant que ce dernier a un plan bien précis en tête', 100, '2022-06-22', 200, 226, 'Anglais', 'buzzLEclair.jpg', 'https://www.youtube.com/watch?v=PXfW7wpxguk', 20),
('Harry Potter - Retour à Poudlard','20 ans après la sortie du 1er volet de la saga Harry Potter au cinéma, les acteurs se réunissent pour rendre hommage à tous ceux dont la vie a été touchée par ce phénomène culturel.',90,'2022-01-01',250,1000,'Anglais','harryPotter.jpg','https://www.youtube.com/watch?v=RW1pCNY6ljg',22),
('Spider-Man: Across the Spider-Verse','Après avoir retrouvé Gwen Stacy, Spider-Man, le sympathique héros originaire de Brooklyn, est catapulté à travers le Multivers, où il rencontre une équipe de Spider-Héros chargée d''en protéger l''existence. Mais lorsque les héros s''opposent sur la façon de gérer une nouvelle menace, Miles se retrouve confronté à eux et doit redéfinir ce que signifie être un héros afin de sauver les personnes qu''il aime le plus.',120,'2023-05-31',150,680,'Anglais','spiderMan.jpg','https://www.youtube.com/watch?v=KbsZN9n-KX4',22),
('Le Menu','Un couple se rend sur une île isolée pour dîner dans un des restaurants les plus en vogue du moment, en compagnie d’autres invités triés sur le volet. Le savoureux menu concocté par le chef va leur réserver des surprises aussi étonnantes que radicales...',106,'2022-11-23',30,90,'Anglais','leMenu.jpg','https://www.youtube.com/watch?v=evmdPkItZl8',25),


-- Liste 2023

('Oppenheimer','En 1942, convaincus que l’Allemagne nazie est en train de développer une arme nucléaire, les États-Unis initient, dans le plus grand secret, le "Projet Manhattan" destiné à mettre au point la première bombe atomique de l’histoire. Pour piloter ce dispositif, le gouvernement engage J. Robert Oppenheimer, brillant physicien, qui sera bientôt surnommé "le père de la bombe atomique". C’est dans le laboratoire ultra-secret de Los Alamos, au cœur du désert du Nouveau-Mexique, que le scientifique et son équipe mettent au point une arme révolutionnaire dont les conséquences, vertigineuses, continuent de peser sur le monde actuel…',180,'2023-07-19',100,946,'Anglais','oppenheimer.jpg','https://youtu.be/uYPbbksJxIg',26),
('Barbie','A Barbie Land, vous êtes un être parfait dans un monde parfait. Sauf si vous êtes en crise existentielle, ou si vous êtes Ken.',114,'2023-07-19',145,1400,'Anglais','barbie.jpg','https://youtu.be/pBk4NYhWNMM',27),
('Killers of the Flower Moon','Au début du XXème siècle, le pétrole a apporté la fortune au peuple Osage qui, du jour au lendemain, est devenu l’un des plus riches du monde. La richesse de ces Amérindiens attire aussitôt la convoitise de Blancs peu recommandables qui intriguent, soutirent et volent autant d’argent Osage que possible avant de recourir au meurtre…',206,'2023-09-27',200,182,'Anglais','killersOfTheFlowerMoon.jpg','https://youtu.be/EP34Yoxs3FQ',28),
('The Marvels','Carol Danvers, alias Captain Marvel, doit faire face aux conséquences imprévues de sa victoire contre les Krees. Des effets inattendus l’obligent désormais à assumer le fardeau d''un univers déstabilisé. Au cours d’une mission qui la propulse au sein d’un étrange vortex étroitement lié aux actions d’une révolutionnaire Kree, ses pouvoirs se mêlent à ceux de Kamala Khan - alias Miss Marvel, sa super-fan de Jersey City - et à ceux de sa « nièce », la Capitaine Monica Rambeau, désormais astronaute au sein du S.A.B.E.R. D’abord chaotique, ce trio improbable se retrouve bientôt obligé de faire équipe et d’apprendre à travailler de concert pour sauver l''univers. Un seul nom pour cela : "The Marvels" !',105,'2023-11-08',220,200,'Anglais','theMarvels','https://youtu.be/wS_qbDztgVY',29),
('Les Gardiens de la Galaxie 3','Notre bande de marginaux favorite a quelque peu changé. Peter Quill, qui pleure toujours la perte de Gamora, doit rassembler son équipe pour défendre l’univers et protéger l’un des siens. En cas d’échec, cette mission pourrait bien marquer la fin des Gardiens tels que nous les connaissons.',150,'2023-05-03',250,850,'Anglais','lesGardiensDeLaGalaxie3.jpg','https://youtu.be/0RsvBSFm938',30),
('Mission: Impossible - Dead Reckoning Part One','Dans Mission: Impossible - Dead Reckoning Partie 1, Ethan Hunt et son équipe de l’IMF se lancent dans leur mission la plus périlleuse à ce jour : traquer une effroyable nouvelle arme avant que celle-ci ne tombe entre de mauvaises mains et menace l’humanité entière.Le contrôle du futur et le destin du monde sont en jeu. Alors que les forces obscures de son passé ressurgissent, Ethan s’engage dans une course mortelle autour du globe. Confronté à un puissant et énigmatique ennemi, Ethan réalise que rien ne peut se placer au-dessus de sa mission - pas même la vie de ceux qu’il aime.',163,'2023-07-12',290,567,'Anglais','missionImpossibleDeadReckoning.jpg','https://youtu.be/kz34RaRBczI',26),
('Asteroid City','Asteroid City est une ville minuscule, en plein désert, dans le sud-ouest des États-Unis. Nous sommes en 1955. Le site est surtout célèbre pour son gigantesque cratère de météorite et son observatoire astronomique à proximité. Ce week-end, les militaires et les astronomes accueillent cinq enfants surdoués, distingués pour leurs créations scientifiques, afin qu’ils présentent leurs inventions. À quelques kilomètres de là, par-delà les collines, on aperçoit des champignons atomiques provoqués par des essais nucléaires.',105,'2023-06-21',25,46,'Anglais','asteroidCity.jpg','https://youtu.be/9FXCSXuGTF4',32),
('The Whale','Charlie, professeur d''anglais reclus chez lui, tente de renouer avec sa fille adolescente pour une ultime chance de rédemption.',117,'2023-03-08',3,55,'Anglais','theWhale.jpg','https://youtu.be/nWiQodhMvz4',33),
('Les Trois Mousquetaires : D’Artagnan','Du Louvre au Palais de Buckingham, des bas-fonds de Paris au siège de La Rochelle… dans un Royaume divisé par les guerres de religion et menacé d’invasion par l’Angleterre, une poignée d’hommes et de femmes vont croiser leurs épées et lier leur destin à celui de la France.',120,'2023-04-05',70,55,'Francais','lesTroisMousquetaires.jpg','https://www.youtube.com/watch?v=a_OUHJziaoE',34),
('Napoleon','L''ascension et à la chute de l''Empereur Napoléon Bonaparte. Le film retrace la conquête acharnée du pouvoir par Bonaparte à travers le prisme de ses rapports passionnels et tourmentés avec Joséphine, le grand amour de sa vie.',158,'2023-11-22',200,221,'Anglais','napoleon.jpg','https://www.youtube.com/watch?v=A3xaMZZooVs',35),
('John Wick: Chapitre 4','John Wick découvre un moyen de vaincre l’organisation criminelle connue sous le nom de la Grande Table. Mais avant de gagner sa liberté, Il doit affronter un nouvel ennemi qui a tissé de puissantes alliances à travers le monde et qui transforme les vieux amis de John en ennemis.',169,'2023-03-22',100,432,'Anglais','johnWick4.jpg','https://www.youtube.com/watch?v=G79ZBcnuluQ',36),
('Elementaire','Dans la ville d’Element City, le feu, l’eau, la terre et l’air vivent dans la plus parfaite harmonie. C’est ici que résident Flam, une jeune femme intrépide et vive d’esprit, au caractère bien trempé, et Flack, un garçon sentimental et amusant, plutôt suiveur dans l’âme. L’amitié qu’ils se portent remet en question les croyances de Flam sur le monde dans lequel ils vivent...',102,'2023-06-21',200,494,'Anglais','elementaire.jpg','https://www.youtube.com/watch?v=C_oyIKvo5_A',37),
('Héros éternels : Indiana Jones & Harrison Ford','De ses humbles débuts au petit écran à ses légendaires films d’action à grand succès, en passant par ses rôles plus introspectifs, ce nouveau documentaire retrace l’illustre carrière d’Harrison Ford. Suivez son incroyable parcours à travers les années et le monde entier en compagnie des cinéastes et des acteurs qui ont donné vie à Indiana Jones et ont marqué l’histoire du cinéma.',142,'2023-05-12',300,387,'Anglais','herosEternelsIndianaJones.jpg','https://www.youtube.com/watch?v=4tvtYAMPsxI',38),
('Anatomie d’une chute','Sandra, Samuel et leur fils malvoyant de 11 ans, Daniel, vivent depuis un an loin de tout, à la montagne. Un jour, Samuel est retrouvé mort au pied de leur maison. Une enquête pour mort suspecte est ouverte. Sandra est bientôt inculpée malgré le doute : suicide ou homicide ? Un an plus tard, Daniel assiste au procès de sa mère, véritable dissection du couple.',150,'2023-08-23',6,25,'Français','anatomieDUneChute.jpg','https://www.youtube.com/watch?v=4vomBbFSs8g',39),
('Alibi.com 2','Suite de la comédie sur une agence qui invente des alibis pour ses clients.',90,'2023-02-08',20,4,'Français','alibiCom.jpg','https://www.youtube.com/watch?v=TNvXaQrS-e0',40),
('Le Royaume de Naya','Par-delà les hautes Montagnes Noires se cache un royaume peuplé de créatures fantastiques. Depuis des siècles, elles protègent du monde des hommes une source de vie éternelle aux pouvoirs infinis. Jusqu’au jour où Naya, la nouvelle élue de cette forêt enchantée, rencontre Lucas, un jeune humain égaré dans les montagnes. À l’encontre des règles établies depuis des millénaires, ils vont se revoir, sans prendre garde aux conséquences qui s’abattront sur le royaume. L’aventure ne fait que commencer.',99,'2023-02-09',10,18,'Ukrainien','leRoyaumeDeNaya.jpg','https://www.youtube.com/watch?v=jMtuvtAAjC0',41),
('Creed III','Idole de la boxe et entouré de sa famille, Adonis Creed n’a plus rien à prouver. Jusqu’au jour où son ami d’enfance, Damian, prodige de la boxe lui aussi, refait surface. A peine sorti de prison, Damian est prêt à tout pour monter sur le ring et reprendre ses droits. Adonis joue alors sa survie, face à un adversaire déterminé à l’anéantir.',117,'2023-03-01',75,275,'Anglais','Creed3.jpg','https://www.youtube.com/watch?v=IJWSR9WyWtA',43),
('Super Mario Bros. Movie','Alors qu’ils tentent de réparer une canalisation souterraine, Mario et son frère Luigi, tous deux plombiers, se retrouvent plongés dans un nouvel univers féerique à travers un mystérieux conduit. Mais lorsque les deux frères sont séparés, Mario s’engage dans une aventure trépidante pour retrouver Luigi.Dans sa quête, il peut compter sur l’aide du champignon Toad, habitant du Royaume Champignon, et les conseils avisés, en matière de techniques de combat, de la Princesse Peach, guerrière déterminée à la tête du Royaume.C’est ainsi que Mario réussit à mobiliser ses propres forces pour aller au bout de sa mission.',92,'2023-04-05',100,1000,'Anglais','superMarioBros.jpg','https://www.youtube.com/watch?v=e15KncPGylY',44),
('Babylon','Los Angeles des années 1920. Récit d’une ambition démesurée et d’excès les plus fous, BABYLON retrace l’ascension et la chute de différents personnages lors de la création d’Hollywood, une ère de décadence et de dépravation sans limites.',189,'2023-01-18',110,63,'Anglais','babylon.jpg','https://www.youtube.com/watch?v=50P1-oPvZOg',46),


-- Liste 2024 ( Listes bleus = pas de réalisateurs / budget et box office ) 

('Vice versa 2','Fraichement diplômée, Riley est désormais une adolescente, ce qui n’est pas sans déclencher un chamboulement majeur au sein du quartier général qui doit faire face à quelque chose d’inattendu : l’arrivée de nouvelles émotions ! Joie, Tristesse, Colère, Peur et Dégoût - qui ont longtemps fonctionné avec succès - ne savent pas trop comment réagir lorsqu’Anxiété débarque. Et il semble qu''elle ne soit pas la seule...',96,'2024-06-19',200,1698,'Anglais','viceVersa2.jpg','https://youtu.be/Jlme9-wFQxk',47),
('Deadpool & Wolverine','Après avoir échoué à rejoindre l’équipe des Avengers, Wade Wilson passe d’un petit boulot à un autre sans vraiment trouver sa voie. Jusqu’au jour où un haut gradé du Tribunal des Variations Anachroniques lui propose une mission digne de lui… à condition de voir son monde et tous ceux qu’il aime être anéantis.Refusant catégoriquement, Wade endosse de nouveau le costume de Deadpool et tente de convaincre Wolverine de l’aider à sauver son univers…',127,'2024-07-24',200,1337,'Anglais','deadpoolEtWolverine.jpg','https://youtu.be/AAWJ21wLN4A',48),
('Moi, moche et méchant 4','Pour la première fois en sept ans, Gru, le super méchant le plus populaire du monde, devenu super agent de l''Agence Vigilance de Lynx, revient dans un nouveau chapitre aussi hilarant que chaotique de la célébrissime saga d’illumination : MOI, MOCHE ET MÉCHANT 4.Gru, Lucy et les filles, Margo, Edith et Agnès accueillent le petit dernier de la famille, Gru Junior, qui semble n’avoir qu’une passion : faire tourner son père en bourrique. Mais Gru est confronté à un nouvel ennemi Maxime Le Mal qui, avec l’aide de sa petite amie, la fatale Valentina, va obliger toute la famille à fuir.',95,'2024-07-10',100,968.2,'Anglais','moiMocheEtMechant4.jpg','https://youtu.be/5HgS6G0xhLY',49),
('Dune : Deuxième partie','Dans DUNE : DEUXIÈME PARTIE, Paul Atreides s’unit à Chani et aux Fremen pour mener la révolte contre ceux qui ont anéanti sa famille. Hanté par de sombres prémonitions, il se trouve confronté au plus grand des dilemmes : choisir entre l’amour de sa vie et le destin de l’univers.',166,'2024-02-28',190,711.8,'Anglais','Dune2.jpg','https://youtu.be/e-_NMJ1JIbk',50),
('Godzilla x Kong : Le Nouvel Empire','Le tout-puissant Kong et le redoutable Godzilla unissent leurs forces contre une terrible menace encore secrète qui risque de les anéantir et qui met en danger la survie même de l’espèce humaine. GODZILLA X KONG : LE NOUVEL EMPIRE remonte à l’origine des deux titans et aux mystères de Skull Island, tout en révélant le combat mythique qui a contribué à façonner ces deux créatures hors du commun et lié leur sort à celui de l’homme pour toujours…',115,'2024-04-03',135,567.7,'Anglais','godzillaXKong.jpg','https://youtu.be/A_bERKuGCJM',51),
('Kung fu panda 4','Après trois aventures dans lesquelles le guerrier dragon Po a combattu les maîtres du mal les plus redoutables grâce à un courage et des compétences en arts martiaux inégalés, le destin va de nouveau frapper à sa porte pour … l’inviter à enfin se reposer. Plus précisément, pour être nommé chef spirituel de la vallée de la Paix. Cela pose quelques problèmes évidents. Premièrement, Po maîtrise aussi bien le leadership spirituel que les régimes, et deuxièmement,il doit rapidement trouver et entraîner un nouveau guerrier dragon avant de pouvoir profiter des avantages de sa prestigieuse promotion. Pire encore, il est question de l’apparition récente d’une sorcière aussi mal intentionnée que puissante, Caméléone, une lézarde minuscule qui peut se métamorphoser en n''importe quelle créature, et ce sans distinction de taille. Or Caméléone lorgne de ses petits yeux avides et perçants sur le bâton de sagesse de Po, à l’aide duquel elle espère bien pouvoir réinvoquer du royaume des esprits tous les maîtres maléfiques que notre guerrier dragon a vaincu. Po va devoir trouver de l’aide. Il va en trouver (ou pas ?) auprès de Zhen, une renarde corsac, voleuse aussi rusée que vive d''esprit, qui a le don d’irriter Po mais dont les compétences vont s’avérer précieuses. Afin de réussir à protéger la Vallée de la Paix des griffes reptiliennes de Caméléone, ce drôle de duo va devoir trouver un terrain d’entente. Ce sera l’occasion pour Po de découvrir que les héros ne sont pas toujours là où on les attend.',93,'2024-03-27',85,548.9,'Anglais','kungFuPanda4.jpg','https://youtu.be/-5qyM5TSWuY',52),
('Sonic 3','Sonic, Knuckles et Tails se retrouvent face à un nouvel adversaire, Shadow, mystérieux et puissant ennemi aux pouvoirs inédits. Dépassée sur tous les plans, la Team Sonic va devoir former une alliance improbable pour tenter d’arrêter Shadow et protéger notre planète.',109,'2024-12-25',85,405,'Anglais','sonic3.jpg','https://youtu.be/KtF7DvnsEig',53),
('Le comte de Monte-Cristo','Victime d’un complot, le jeune Edmond Dantès est arrêté le jour de son mariage pour un crime qu’il n’a pas commis. Après quatorze ans de détention au château d’If, il parvient à s’évader. Devenu immensément riche, il revient sous l’identité du comte de Monte-Cristo pour se venger des trois hommes qui l’ont trahi.',173,'2024-06-28',42.9,100,'Français','leCompteDeMonteCristo.jpg','https://www.youtube.com/watch?v=-7SFnhQFrnI',54),
('Smile 2','À l’aube d’une nouvelle tournée mondiale, la star de la pop Skye Riley se met à vivre des événements aussi terrifiants qu’inexplicables. Submergée par la pression de la célébrité et devant un quotidien qui bascule de plus en plus dans l’horreur, Skye est forcée de se confronter à son passé obscur pour tenter de reprendre le contrôle de sa vie avant qu’il ne soit trop tard.',132,'2024-10-16',30,23,'Anglais','smile2.jpg','https://www.youtube.com/watch?v=x-0e8laDdsA',55),
('Spaceman','Au bout de six mois d’une mission de recherche aux confins du système solaire, totalement coupé du monde, l’astronaute Jakub découvre que la femme qu’il a laissée derrière lui ne l’attendra peut-être pas à son retour sur Terre. Alors qu’il cherche à clarifier la situation avec son épouse Lenka, une mystérieuse créature ancestrale, nichée dans les entrailles de son vaisseau, lui vient en aide. Hanuš collabore avec Jakub pour comprendre ce qui s’est passé avant qu’il ne soit trop tard.',107,'2024-03-01',40,null,'Anglais','spaceman.jpg','https://www.youtube.com/watch?v=Mtr36Qc3K_w',56),
('Le Salaire de la peur','Une équipe de choc a moins de 24 heures pour convoyer deux camions bourrés d’explosifs à travers une région hostile et empêcher une terrible catastrophe. D’après le film original « Le Salaire de la Peur » réalisé par Henri-Georges Clouzot, coécrit par Jean et Henri-Georges Clouzot, d’après l’ouvrage éponyme de Georges Arnaud publié aux Éditions Julliard.',104,'2024-03-29',null,null,'Français','leSalaireDeLaPeur.jpg','https://www.youtube.com/watch?v=GRrqa3nD2YY',999),
('Ducobu passe au vert!','Nouvelle rentrée à Saint-Potache. Cette année Ducobu a une idée de génie : prendre une année sabbatique pour sauver la planète mais surtout pour sécher l’école ! Mais Latouche ne compte pas le laisser faire si facilement… Tricheur et écolo, c’est pas du gâteau !',80,'2024-04-03',9,7,'Français','ducobuPasseAuVert.jpg','https://www.youtube.com/watch?v=XiwtYHHQM68',58),
('S.O.S. Fantômes - La Menace de glace','La famille Spengler revient là où tout a commencé, l''emblématique caserne de pompiers de New York. Ils vont alors devoir faire équipe avec les membres originels de S.O.S. Fantômes, qui ont mis en place un laboratoire de recherche top secret pour faire passer la chasse aux fantômes à la vitesse supérieure.',115,'2024-04-10',125,150,'Anglais','sosFantomesLaMenaceDeGlace.jpg','https://www.youtube.com/watch?v=mEgrD_CG25c',59),
('Nicky Larson','Tireur d''élite et éternel séducteur, le détective privé Nicky Larson fait équipe à contrecœur avec la sœur de son ancien partenaire pour enquêter sur la mort de ce dernier.',102,'2024-04-25',15,22,'Français','nickyLarson.jpg','https://www.youtube.com/watch?v=Z4VzNdgPkGc',40),
('The Fall Guy','C''est l’histoire d’un cascadeur, et comme tous les cascadeurs, il se fait tirer dessus, exploser, écraser, jeter par les fenêtres et tombe toujours de plus en plus haut… pour le plus grand plaisir du public. Après un accident qui a failli mettre fin à sa carrière, ce héros anonyme du cinéma va devoir retrouver une star portée disparue, déjouer un complot et tenter de reconquérir la femme de sa vie tout en bravant la mort tous les jours sur les plateaux. Que pourrait-il lui arriver de pire ?',126,'2024-05-01',125,127,'Anglais','theFallGuy.jpg','https://www.youtube.com/watch?v=MvlSLYCGpSA',60),
('Furiosa - Une saga Mad Max','Dans un monde en déclin, la jeune Furiosa est arrachée à la Terre Verte et capturée par une horde de motards dirigée par le redoutable Dementus. Alors qu’elle tente de survivre à la Désolation, à Immortan Joe et de retrouver le chemin de chez elle, Furiosa n’a qu’une seule obsession : la vengeance.',148,'2024-05-22',150,172,'Anglais','furiosaUneSagaMadMax.jpg','https://www.youtube.com/watch?v=ZrbkSciPAVU',61),
('Bad Boys: Ride or Die','Cet été, la franchise Bad Boys est de retour avec son mélange iconique d''action explosive et d''humour irrévérencieux. Mais cette fois-ci, les meilleurs flics de Miami deviennent les hommes les plus recherchés d''Amérique.',115,'2024-06-05',200,426,'Anglais','badBoysRideOrDie.jpg','https://www.youtube.com/watch?v=Y7GX3iPk0xE',62),
('Gladiator 2','Des années après avoir assisté à la mort du héros vénéré Maximus aux mains de son oncle, Lucius est forcé d''entrer dans le Colisée lorsque son pays est conquis par les empereurs tyranniques qui gouvernent désormais Rome d''une main de fer. La rage au cœur et l''avenir de l''Empire en jeu, Lucius doit se tourner vers son passé pour trouver la force et l''honneur de rendre la gloire de Rome à son peuple.',148,'2024-11-13',210,320,'Anglais','gladiator2.jpg','https://www.youtube.com/watch?v=zMIb0rQUoaM',35),
('Le Flic de Beverly Hills - Axel F.','L’inspecteur Axel Foley reprend du service à Beverly Hills ! Lorsque la vie de sa fille est en jeu, il fait équipe avec elle, mais aussi avec un nouveau partenaire et ses vieux potes Billy Rosewood et John Taggart, pour mettre au jour un complot. La température risque de monter d’un cran !',115,'2024-07-03',100,400,'Anglais','leFlicDeBeverlyHills.jpg','https://www.youtube.com/watch?v=RTxyKLoosj0',64),
('Garfield - Héros malgré lui','Garfield, le célèbre chat d''intérieur, amateur de lasagnes et qui déteste les lundis, est sur le point d''être embarqué dans une folle aventure ! Après avoir retrouvé son père disparu, Vic, un chat des rues mal peigné, Garfield et son ami le chien Odie sont forcés de quitter leur vie faite de confort pour aider Vic à accomplir un cambriolage aussi risqué qu''hilarant.',101,'2024-07-31',60,58,'Anglais','garfieldHerosMalgreLui.jpg','https://www.youtube.com/watch?v=cldTF5e8wpQ',65),
('Borderlands','Lilith, une chasseuse de primes au passé trouble, revient à contrecœur sur sa planète natale, Pandore, la planète la plus chaotique de la galaxie… Sa mission est de retrouver la fille disparue d''Atlas, l’homme le plus puissant (et le plus méprisable) de l’univers.',102,'2024-08-07',150,250,'Anglais','borderlands.jpg','https://www.youtube.com/watch?v=IF5Fq9wB-fQ',66),
('Beetlejuice Beetlejuice','Après une terrible tragédie, la famille Deetz revient à Winter River. Toujours hantée par le souvenir de Beetlejuice, Lydia voit sa vie bouleversée lorsque sa fille Astrid, adolescente rebelle, ouvre accidentellement un portail menant à l’Après-vie. Alors que le chaos plane sur les deux mondes, ce n’est qu’une question de temps avant que quelqu’un ne prononce le nom de Beetlejuice trois fois et que ce démon farceur ne revienne semer la pagaille…',105,'2024-09-11',70,300,'Anglais','beetlejuiceBeetleJuice.jpg','https://www.youtube.com/watch?v=ltHWSzuq_hY',67);

INSERT INTO Ceremonie(nomFestival,dateCeremonie) VALUES
('Critics'' Choice Movie Awards',2022),
('Saturn Awards',2022),
('Golden Globe Awards',2023),
('Annie Awards',2024),
('Venice Film Festival Award',2022),
('Festival de Cannes',2023),
('Emmy Awards',2023),
('Rising Star Awards',2023),
('Teen Choice Awards',2023),
('Rising Star Awards',2023),
('The Oscars',2023);


INSERT INTO Realisateur(nom, prenom, dateNaissance, nationalite) VALUES
("Bettinelli-Olpin","Matt","1981-02-19","Americaine"),
("Gillett","Tyler","1982-03-16","Americaine"),
("Fleischer","Ruben","1974-10-31","Americaine"),
("Reeves","Matt","1966-04-27","Americaine"),
("Espinosa","Daniel","1977-03-23","Suédoise"),
("Raimi","Sam","1959-10-23","Americaine"),
("Kosinski","Joseph","1974-05-03","Americaine"),
("Trevorrow","Colin","1976-09-13","Americaine"),
("Balda","Kyle","1971-05-09","Americaine"),
("Ableson","Brad","1975-06-14","Americaine"),
("Waititi","Taika","1975-08-16","Néo-zélandaise"),
("Coogler","Ryan","1986-05-23","Americaine"),
("Cameron","James","1954-08-16","Canadienne"),
("Peele","Jordan","1979-02-21","Americaine"),
("Muschietti","Andy","1973-08-26","Argentaine"),
("Collet-Serra","Jaume","1974-03-23","Espagnole"),
("Wan","James","1977-02-26","Malaisienne-Australienne"),
("Crawford","Joel","1985-05-17","Americaine"),
("Mercado","Januel","1984-11-23","Americaine"),
("McLane","Angus","1972-04-13","Americaine"),
("Petterson","Casey","1965-05-11","Americaine"),
("Dos Sanos","Joaquim","1977-11-22","Portugaise"),
("Powers","Kemp","1973-10-03","Americaine"),
("Thompson","Justin","1981-01-09","Americaine"),
("Mylod","Mark","1965-06-01","Britannique"),
("Nolan,","Christopher","1970-07-30","Britannique-Americaine"),
("Gerwig","Greta","1983-08-04","Americaine"),
("Scorsese","Martin","1942-11-17","Americaine"),
("DaCosta","Nia","1989-11-08","Americaien"),
("Gunn","James","1966-08-05","Americaine"),
("McQuarrie","Christopher","1968-05-31","Americaine"),
("Anderson","Wes","1969-05-01","Americaine"),
("Aronofsky","Darren","1969-02-12","Americaine"),
("Bourboulon","Martin","1979-06-27","Française"),
("Scott","Ridley","1937-11-30","Britannique"),
("Stahelski","Colin","1968-09-20","Americaine"),
("Sohn","Peter","1977-10-23","Americaine"),
("Mangold","James","1963-12-16","Americaine"),
("Triet","Justine","1978-07-17","Française"),
("Lacheau","Philippe","1980-06-05","Française"),
("Malamuzh","Oleg","1978-05-05","Ukrainienne"),
("Ruban","Oleksandra","1965-05-11","Ukrainienne"),
("B. Jordan","Michael","1987-02-09","Americaine"),
("Horvath","Aaron","1980-08-19","Americaine"),
("Jelenic","Michael","1977-05-12","Americaine"),
("Chazelle","Damien","1985-01-19","Française-Americaine"),
("Mann","Kelsey","1974-11-16","Americaine"),
("Levy","Shawn","1968-07-23","Canadienne"),
("Chris","Renaud","1966-12-25","Americaine"),
("Villeneuve","Denis","1967-10-03","Canadienne"),
("Wingard","Adam","1982-12-03","Americaine"),
("Mitchell","Mike","1970-11-18","Américaine"),
("Fowler","Jeff","1978-07-27","Americaine"),
("Goyer","David","1965-10-22","Americaien"),
("Finn","Parker","1987-03-18","Americaine"),
("Renck","Johan","1966-12-05","Suédoise"),
("Friedkin","William","1935-08-25","Americaine"),
("Semoun","Elie","1963-10-16","Française"),
("Kenan","Gil","1976-10-16","Britannique-Americaine"),
("Leitch","David","1969-12-16","Americaine"),
("Miller","George","1945-03-03","Australienne"),
("El Arbi","Adil","1988-06-30","Belge"),
("Fillah","Billal","1986-01-04","Belge"),
("Molloy","Mark","1965-05-11","Autralienne"),
("Dindal","Mark","1960-08-14","Americaine"),
("Roth","Eli","1972-04-18","Americaine"),
("Burton","Tim","1958-08-25","Americaine");

INSERT INTO Acteur(nom, prenom, dateNaissance, nationalite) VALUES
("Campbell","Neve","1973-10-03","Canadien"),
("Holland","Tom","1996-06-01","Britannique"),
("Pattinson","Robert","1986-05-13","Britannique"),
("Leto","Jared","1971-12-26","Américaine"),
("Cumberbatch","Benedict","1976-07-19","Britannique"),
("Cruise","Tom","1962-07-03","Américaine"),
("Pratt","Chris","1979-06-21","Américaine"),
("Carell","Steve","1962-08-16","Américaine"),
("Hemsworth","Chris","1983-08-11","Australienne"),
("Wright","Letitia","1993-10-31","Britannique"),
("Worthington","Sam","1976-08-02","Australienne"),
("Kaluuya","Daniel","1989-02-24","Britannique"),
("Miller","Ezra","1992-09-30","Américaine"),
("Jhonson","Dwayne","1972-05-02","Américaine"),
("Momoa","Jason","1979-08-01","Américaine"),
("Banderas","Antonio","1960-08-10","Espagnoel"),
("Evans","Chris","1981-06-13","Américaine"),
("Radcliffe","Daniel","1989-07-23","Britannique"),
("Moore","Shameik","1995-05-04","Américaien"),
("Murphy","Cillian","1976-05-25","Irlandaise"),
("Robbie","Margot","1990-07-02","Australienne"),
("DiCaprio","Leonardo","1974-11-11","Américaine"),
("Larson","Brie","1989-10-01","Américaine"),
("Schwartzaman","Larson","1989-06-26","Américaine"),
("Fraser","Brendan","1968-12-03","Canadienne-Américaine"),
("Duris","Romain","1977-05-28","Française"),
("Phoenix","Joaquin","1974-10-28","Américaine"),
("Reeves","Keanu","1964-09-02","Canadienne"),
("Lewis","Leah","1996-12-21","Américaine"),
("Ford","Harrison","1942-07-13","Américaine"),
("Hüller","Sandra","1978-04-30","Allemande"),
("Lacheau","Philippe","1980-06-25","Française"),
("Jordan","Michal B.","1987-02-09","Américaine"),
("Day","Charlie","1976-02-09","Américaine"),
("Pitt","Brad","1963-12-18","Américaine"),
("Poehler","Amy","1971-09-16","Américaine"),
("Reynolds","Ryan","1976-10-23","Canadienne"),
("Chalamet","Timothée","1995-12-27","Français-Américain"),
("Brown","Millie Bobby","2004-02-19","Britannique"),
("Black","Jack","1969-08-28","Américaine"),
("Black","Jack","1969-08-28","Américaine"),
("Schwartz","Ben","1981-09-15","Américaine"),
("Cavill","Henry","1983-05-05","Britannique"),
("Bacon","Kevin","1958-07-08","Américaine"),
("Sandler","Adam","1966-09-09","Américaine"),
("Bardem","Javier","1969-03-01","Espagnole"),
("Rudd","Paul","1969-04-06","Américaine"),
("Gosling","Ryan","1980-11-12","Canadienne"),
("Taylor-joy","Anya","1996-04-16","Américaine"),
("Smith","Will","1968-05-25","Américaine"),
("Mescal","Paul","1996-02-02","Irlandaise"),
("Murphy","Eddie","1961-04-03","Américaine"),
("Blanchett","Cate","1969-05-14","Australienne"),
("Keaton","Michael","1951-09-05","Américaine");

INSERT INTO Jouer (idFilm, idActeur, roleJoue) VALUES 
(1, 1, 'Sydney Prescott'),                    -- Scream
(2, 2, 'Nathan Drake'),                       -- Uncharted
(3, 3, 'Bruce Wayne / Batman'),               -- The Batman
(4, 4, 'Dr. Michael Morbius'),                -- Morbius
(5, 5, 'Doctor Strange'),                     -- Doctor Strange
(6, 6, 'Pete "Maverick" Mitchell'),           -- Top Gun: Maverick
(7, 7, 'Owen Grady'),                         -- Jurassic World
(8, 8, 'Gru'),                               -- Moi, moche et méchant
(9, 9, 'Thor'),                              -- Thor
(10, 10, 'Shuri'),                           -- Black Panther
(11, 11, 'Jake Sully'),                      -- Avatar
(12, 12, 'OJ Haywood'),                      -- Nope
(13, 13, 'Barry Allen / The Flash'),         -- The Flash
(14, 14, 'Black Adam'),                      -- Black Adam
(15, 15, 'Aquaman / Arthur Curry'),           -- Aquaman
(16, 16, 'Le Chat Potté'),                   -- Le Chat Potté
(17, 17, 'Buzz l\'éclair'),                  -- Buzz l'éclair
(18, 18, 'Harry Potter'),                    -- Harry Potter
(19, 19, 'Miles Morales'),                   -- Spider-Man: New Generation
(20, 20, 'J. Robert Oppenheimer'),           -- Oppenheimer
(21, 21, 'Barbie'),                          -- Barbie
(22, 22, 'Ernest Burkhart'),                 -- Killers of the Flower Moon
(23, 23, 'Carol Danvers / Captain Marvel'),  -- Captain Marvel
(24, 7, 'Peter Quill / Star-Lord'),          -- Les Gardiens de la Galaxie
(25, 6, 'Ethan Hunt'),                       -- Mission: Impossible
(26, 24, 'Augie Steenbeck'),                 -- The Killer
(27, 25, 'Charlie'),                         -- Charlie
(28, 26, 'Aramis'),                          -- Les Trois Mousquetaires
(29, 27, 'Napoléon Bonaparte'),              -- Napoléon
(30, 28, 'John Wick'),                       -- John Wick
(31, 29, 'Ember Lumen'),                     -- Elemental
(32, 30, 'Indiana Jones'),                   -- Indiana Jones
(33, 31, 'Sandra'),                          -- Sandra
(34, 32, 'Greg'),                            -- Greg
(35, NULL, NULL),                            -- Placeholder
(36, 33, 'Adonis Creed'),                    -- Creed
(37, 34, 'Donkey Kong'),                     -- Super Mario Bros.
(38, 35, 'Jack Conrad'),                     -- The Creator
(39, 36, 'Joie'),                            -- Joie
(40, 37, 'Wade Wilson / Deadpool'),          -- Deadpool
(41, 8, 'Gru'),                             -- Moi, moche et méchant
(42, 38, 'Paul Atreides'),                   -- Dune
(43, 39, 'Bernie Hayes'),                    -- Transformers
(44, 40, 'Po'),                              -- Kung Fu Panda
(45, 41, 'Knuckles le Hérisson'),            -- Sonic 2
(46, 42, 'Edmond Dantès'),                   -- Le Comte de Monte-Cristo
(47, 43, 'Jimmy'),                           -- Jimmy
(48, 44, 'Jakub Procházka'),                 -- The Gray Man
(49, 45, 'Mario'),                           -- Super Mario Bros.
(50, 32, 'Ducobu'),                          -- Ducobu
(51, 46, 'Gary Grooberson'),                -- Ghostbusters: L'Héritage
(52, 32, 'Nicky Larson'),                    -- Nicky Larson
(53, 47, 'Stuntman'),                        -- Stuntman
(54, 48, 'Furiosa'),                         -- Furiosa
(55, 49, 'Mike Lowrey'),                     -- Bad Boys
(56, 50, 'Lucius'),                          -- Lucius
(57, 51, 'Axel Foley'),                     -- Beverly Hills Cop
(58, 7, 'Garfield'),                        -- Garfield
(59, 52, 'Lilith'),                          -- Lilith
(60, 5, 'Beetlejuice');                      -- Beetlejuice



INSERT INTO Genre (libelle,descri) VALUES 
('Action','Le genre action se caractérise par des scènes dynamiques et intenses, souvent marquées par des combats, des poursuites et des explosions. Il met en avant des héros confrontés à des défis physiques ou des ennemis, avec un rythme rapide et spectaculaire.'),
('Animation','Le genre animation englobe des films créés à partir de dessins, d''images générées par ordinateur ou de techniques d''animation, souvent destinés à tous les âges. Il permet de raconter des histoires imaginatives ou fantastiques dans des styles visuels variés.'),
('Aventure','Le genre aventure met en scène des quêtes captivantes, des explorations ou des périples dans des lieux souvent exotiques ou dangereux. Il se concentre sur l’action, le voyage et les défis à surmonter, tout en offrant un sentiment d’évasion et d’excitation.'),
('Biopic','Le biopic est un genre qui raconte la vie d''une personne réelle, souvent célèbre ou influente, en mettant en lumière ses accomplissements, ses luttes personnelles et son impact sur le monde. Il cherche à être fidèle à la réalité tout en ajoutant une dimension narrative captivante.'),
('Comédie','La comédie est un genre conçu pour divertir et faire rire, souvent à travers des situations absurdes, des dialogues humoristiques ou des personnages décalés. Elle peut explorer divers aspects de la vie quotidienne ou des contextes exagérés, avec pour objectif principal de provoquer le rire et la légèreté.'),
('Crime','Le genre crime se concentre sur les activités criminelles, qu''il s''agisse de braquages, de meurtres, ou d''enquêtes pour résoudre des délits. Il explore souvent les motivations des criminels, les dilemmes des enquêteurs, et les conséquences de ces actes dans des récits captivants.'),
('Documentaire','Le documentaire est un genre qui vise à informer ou à éduquer en présentant des faits réels, souvent à travers des images, des interviews et des recherches approfondies. Il peut aborder des sujets variés comme l''histoire, la nature, la société ou des individus remarquables, tout en cherchant à captiver le spectateur.'),
('Drame','Le drame est un genre qui explore les émotions et les expériences humaines de manière intense et réaliste. Il met l''accent sur les conflits personnels, sociaux ou moraux, en racontant des histoires profondes qui touchent souvent aux luttes ou aux relations humaines.'),
('Espionnage','Le genre espionnage se concentre sur des intrigues liées au monde du renseignement, des agents secrets et des missions clandestines. Il mêle suspense, action et manipulation, souvent dans un contexte de géopolitique ou de guerre froide.'),
('Fantastique','Le fantastique est un genre qui mêle la réalité à des éléments surnaturels ou magiques. Ces récits se déroulent souvent dans des mondes imaginaires ou intègrent des créatures, des pouvoirs ou des événements inexplicables, tout en laissant une place à l''émerveillement et à l''inattendu.'),
('Historique','Le genre historique se concentre sur des événements, des personnages ou des périodes marquantes du passé. Il cherche à recréer l''atmosphère et les contextes culturels d''une époque, souvent avec un souci d''authenticité, tout en racontant une histoire captivante.'),
('Horreur','L''horreur est un genre conçu pour effrayer, perturber ou provoquer l''angoisse. Il met en scène des éléments effrayants comme des monstres, des forces surnaturelles ou des tueurs, et joue souvent sur nos peurs profondes à travers une ambiance sombre et oppressante.'),
('Romance','La romance est un genre qui se concentre sur les relations amoureuses et les émotions qui en découlent. Elle explore les thèmes de l’amour, des rencontres, des obstacles à surmonter et des liens entre les personnages, souvent avec une fin heureuse ou émouvante.'),
('Science-fiction','La science-fiction est un genre qui imagine des mondes futuristes, des technologies avancées ou des réalités alternatives. Elle explore des thèmes comme les voyages spatiaux, l''intelligence artificielle, les extraterrestres ou les impacts sociétaux de découvertes scientifiques. Ce genre pousse souvent à réfléchir sur l''humanité et son avenir.'),
('Sport','Le genre sport met en avant des histoires centrées sur des compétitions sportives, des athlètes ou des équipes. Il explore souvent des thèmes comme le dépassement de soi, la persévérance, la camaraderie et parfois les défis personnels ou sociaux liés à la pratique d''un sport.'),
('Super-héros','Le genre super-héros met en scène des personnages dotés de pouvoirs extraordinaires ou d''aptitudes exceptionnelles, souvent engagés dans une lutte contre le mal ou pour protéger l’humanité. Ces récits sont généralement basés sur des bandes dessinées et explorent des thèmes comme le courage, la responsabilité et l’identité.'),
('Thriller','Le thriller est un genre qui se caractérise par une tension constante, du suspense et des retournements de situation. Il maintient le spectateur dans un état d''anticipation, souvent en explorant des intrigues complexes où les protagonistes font face à des dangers, des mystères ou des ennemis redoutables.'),
('Comedie noire','La comédie noire est un genre qui mélange l''humour et des thèmes sombres ou tabous, souvent en abordant des sujets sérieux ou macabres avec un ton ironique ou satirique. Elle invite à rire de situations inconfortables ou tragiques, tout en provoquant une réflexion sur des aspects absurdes ou cruels de la vie.');

INSERT INTO AppartenirGenre (idFilm, idGenre) VALUES
(1, 13), (1, 17),   -- Scream -> Horreur, Thriller
(2, 1), (2, 3),     -- Uncharted -> Action, Aventure
(3, 1), (3, 17),    -- The Batman -> Action, Thriller
(4, 10), (4, 14),   -- Morbius -> Fantastique, Science-fiction
(5, 10), (5, 14),   -- Doctor Strange -> Fantastique, Science-fiction
(6, 1), (6, 8),     -- Top Gun: Maverick -> Action, Drame
(7, 1), (7, 14),    -- Jurassic World -> Action, Science-fiction
(8, 2),             -- Les minions 2 -> Animation
(9, 1), (9, 10),    -- Thor: Love and Thunder -> Action, Fantastique
(10, 1), (10, 10),  -- Black Panther -> Action, Fantastique
(11, 1), (11, 14),  -- Avatar 2 -> Action, Science-fiction
(12, 17),           -- Nope -> Thriller
(13, 1), (13, 14),  -- The Flash -> Action, Science-fiction
(14, 1), (14, 10),  -- Black Adam -> Action, Fantastique
(15, 1), (15, 10),  -- Aquaman -> Action, Fantastique
(16, 2),            -- Le Chat Potté 2 -> Animation
(17, 2), (17, 3),   -- Buzz l’Éclair -> Animation, Aventure
(18, 10), (18, 3),  -- Harry Potter -> Fantastique, Aventure
(19, 2), (19, 1),   -- Spider-Man: Across the Spider-Verse -> Animation, Action
(20, 17), (20, 8), (20, 18), -- Le menu -> Thriller, Drame, Comédie noire
(21, 9), (21, 8),   -- Oppenheimer -> Biopic, Drame
(22, 5),            -- Barbie -> Comédie
(23, 9), (23, 8),   -- Killers of the Flower Moon -> Biopic, Drame
(24, 1), (24, 14),  -- The Marvels -> Action, Science-fiction
(25, 1), (25, 14),  -- Les Gardiens de la Galaxie Vol. 3 -> Action, Science-fiction
(26, 1), (26, 17),  -- Mission: Impossible -> Action, Thriller
(27, 5), (27, 8), (27, 13), -- Asteroid city -> Comédie, drame, romance
(28, 8),            -- The Whale -> Drame
(29, 3), (29, 1),   -- Les Trois Mousquetaires -> Aventure, Action
(30, 9),            -- Napoléon -> Biopic
(31, 1),            -- John Wick 4 -> Action
(32, 2), (33, 8),   -- Élémentaire -> Animation, Drame
(33, 3), (33, 1),   -- Indiana Jones -> Aventure, Action
(34, 8), (34, 17),  -- Anatomie d'une chute -> Drame, thriller
(35, 5),            -- Alibi.com -> Comédie
(36, 3), (36, 2), (36, 10), -- Le royaume de Naya -> Aventure, animation, fantastique
(37, 1), (37, 8),   -- Creed III -> Action, Drame
(38, 2),            -- Super Mario Bros -> Animation
(39, 8), (39, 5), (39, 11), -- Babylon -> Drame, comédie, historique
(40, 2),            -- Vice-Versa 2 -> Animation
(41, 1), (41, 5),   -- Deadpool 3 -> Action, Comédie
(42, 2),            -- Moi, moche et méchant 4 -> Animation
(43, 14), (43, 8),  -- Dune 2 -> Science-fiction, Drame
(44, 14), (44, 1), (44, 3), -- Godzilla -> Science-fiction, Action, aventure
(45, 2), (45, 5),   -- Kung Fu Panda 4 -> Animation, Comédie
(46, 14), (46, 1),  -- Sonic 3 -> Science-fiction, Action
(47, 3), (47, 8),   -- Le Comte de Monte-Cristo -> Aventure, Drame
(48, 12), (48, 17), -- Smile 2 -> Horreur, Thriller
(49, 8), (49, 14),  -- Spaceman -> Drame, Science-fiction
(50, 3), (50, 8),   -- Le salaire de la peur -> Aventure, Drame
(51, 5) ,           -- Ducobu -> Comédie
(52, 5), (52, 10), (52, 3), -- SOS fantomes -> Comédie, fantastique, Aventure
(53, 1), (53, 5), -- Nicky Larson -> Action, Comédie
(54, 1), (54, 5), (54, 3),  -- The fall guy -> Action, Comédie, Aventure
(55, 1), (55, 3), (55, 14), -- Furiosa -> Action, Aventure, Science-fiction
(56, 1), (56, 5),   -- Bad boys -> Action, Comédie
(57, 11), (57, 8), (57, 3), -- Gladiator 2 -> Historique, Drame, Aventure
(58, 1), (58, 5),   -- Le flic de Berverly Hills -> Action, Comédie
(59, 2), (59, 5), -- Garfield -> Animation, Comédie
(60, 14), (60, 1), (60, 3), -- Borderlands -> Science-fction, Action, Aventure
(61, 10), (61, 18); -- Beetlejuice Beetlejuice -> Fantatisque, Comédie noire


INSERT INTO Avis(note, commentaire, datePublication, idUtilisateur, idFilm) VALUES
(2.5, 'blablabla', CURDATE(), 1, 1),
(2, 'blablabla', CURDATE(), 2, 2),
(3, 'blablabla', CURDATE(), 3, 3),
(3, 'blablabla', CURDATE(), 3, 3),
(4, 'blablabla', CURDATE(), 1, 4),
(5, 'blablabla', CURDATE(), 4, 5),
(1.5, 'blablabla', CURDATE(), 4, 6),
(3.5, 'blablabla', CURDATE(), 2, 7),
(2, 'blablabla', CURDATE(), 3, 8),
(2.5, 'blablabla', CURDATE(), 5, 9),
(4.5, 'blablabla', CURDATE(), 2, 10);


INSERT INTO Utilisateur (nom, prenom, pseudo, email, motDePasse, estAdmin) VALUES
('Gaétan', 'Simonet', 'IKIANA', 'gsimonet63.pro@gmail.com', '	', true),
('Kilian', 'Montagné', 'Kilian', 'user1@mail.com', 'MotDePasse', true),
('Dorian', 'Schneider', 'Dorian', 'user2@mail.com', 'MotDePasse', true),
('Hamza', 'Louza', 'Hamza', 'user4@mail.com', 'MotDePasse', true);

INSERT INTO Watchlist(idUtilisateur, idFilm, dateAjout) VALUES 
(1, 1, CURDATE()),
(1, 2, CURDATE()),
(2, 3, CURDATE()),
(2, 4, CURDATE()),

(3, 5, CURDATE()),
(3, 6, CURDATE()),
(3, 20, CURDATE()),
(3, 30, CURDATE()),
(3, 40, CURDATE()),

(4, 8, CURDATE()),
(4, 9, CURDATE()),
(5, 10, CURDATE()),
(5, 11, CURDATE()),
(6, 12, CURDATE()),
(6, 13, CURDATE()),
(7, 14, CURDATE()),
(7, 15, CURDATE()),
(1, 16, CURDATE());
