# AI Spam Comment Detector

## Description

The **AI Spam Comment Detector** uses **OpenAI's GPT-4** to detect spam comments on your WordPress website. It prevents spam by analyzing comments in real-time and gives an inline message to users when their comment is flagged as spam.

This plugin provides an automatic solution for spam comment detection, reducing reliance on traditional rule-based systems like CAPTCHA.

## Features

🧠 **Smart Detection**: Uses context-aware GPT-4 language model  
🚫 **Auto-Block Spam**: Flags or blocks comments before submission  
🔔 **Inline Error Messaging**: Warns users above the comment form  
🔐 **Private & Secure**: No data stored externally beyond OpenAI API  
🔧 **API Key Config**: Add your own OpenAI API key from plugin settings 

#### No more CAPTCHAs. No more bots. Just intelligent spam protection.

## Installation

1. Upload the `ai-spam-comment-detector` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to the settings page under `Settings > AI Spam Detector` to enter your OpenAI API key.
4. Save the settings, and you're ready to go!

## Frequently Asked Questions

### What is the OpenAI API key used for?

The OpenAI API key is used to connect to OpenAI's GPT-4 model for spam detection. You can obtain your API key by signing up at [OpenAI](https://platform.openai.com/).

### How does this plugin work?

The plugin checks comments against GPT-4 in real-time before submission. If the comment is flagged as spam, a warning message will appear for the user, and the comment will not be submitted.

### Can I use this plugin without an API key?

No, an OpenAI API key is required to use the spam detection feature.

### What happens if my OpenAI quota is exceeded?

If your quota is exceeded, the plugin will notify you with a message saying "OpenAI quota exceeded."

### Will this replace reCAPTCHA or Akismet? 
Yes, it can replace both if you're looking for a no-friction, intelligent spam filter powered by AI.

### Is any user data sent to OpenAI?
Only the comment content is sent securely to OpenAI for spam analysis. No personal data or identifiable information is sent.

### Can I customize the AI behavior?
Currently, this plugin uses a fixed system prompt for high accuracy. Customization options will come in future updates.

## Changelog

### 1.0
- Initial release with basic spam detection using OpenAI GPT-4.

## License

This plugin is licensed under the GPL-2.0 license. See the [full license](http://www.gnu.org/licenses/gpl-2.0.html) for more details.