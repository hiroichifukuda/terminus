# Terminus\Models\Backup

### __construct
##### Description:
    Object constructor

##### Parameters:
    [object] $attributes Attributes of this model
    [array]  $options    Options with which to configure this model

---

### backupIsFinished
##### Description:
    Determines whether the backup has been completed or not

##### Return:
    [bool] True if backup is completed.

---

### getBucket
##### Description:
    Returns the bucket name for this backup

##### Return:
    [string]

---

### getDate
##### Description:
    Returns the date the backup was completed

##### Return:
    [string] Timestamp completion time or "Pending"

---

### getElement
##### Description:
    Returns the element type of the backup

##### Return:
    [string] code, database, files, or null

---

### getInitiator
##### Description:
    Returns the type of initiator of the backup

##### Return:
    [string] Either "manual" or "automated"

---

### getSizeInMb
##### Description:
    Returns the size of the backup in MB

##### Return:
    [string] A number (int or float) followed by 'MB'.

---

### getUrl
##### Description:
    Gets the URL of a backup

##### Return:
    [string]

---

