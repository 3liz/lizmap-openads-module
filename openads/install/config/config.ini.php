[modules]

openads.enabled=2

[coordplugins]
jacl2=1
auth="index/auth.coord.ini.php"

[coordplugin_jacl2]
on_error=2
error_message="jacl2~errors.action.right.needed"
on_error_action="jelix~error:badright"
