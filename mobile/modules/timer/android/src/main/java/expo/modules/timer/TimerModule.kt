package expo.modules.timer

import android.content.Intent
import android.provider.AlarmClock
import expo.modules.kotlin.modules.Module
import expo.modules.kotlin.modules.ModuleDefinition

class TimerModule : Module() {
    override fun definition() = ModuleDefinition {
        Name("Timer")

        Function("setTimer") { seconds: Int, message: String ->
            val intent = Intent(AlarmClock.ACTION_SET_TIMER).apply {
                putExtra(AlarmClock.EXTRA_LENGTH, seconds)
                putExtra(AlarmClock.EXTRA_MESSAGE, message)
                putExtra(AlarmClock.EXTRA_SKIP_UI, true)
                addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
            }
            appContext.currentActivity?.startActivity(intent)
        }
    }
}
