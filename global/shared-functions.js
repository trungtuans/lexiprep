(function () {
  "use strict";
  // Convert html to markdown or text
  function htmlToMarkdown(htmlString, cleanup = false) {
    // Create an instance of TurndownService with default options.
    const turndownService = new TurndownService();
    let markdown = turndownService.turndown(htmlString);

    if (cleanup) {
      // Remove markdown links: transform [text](url) to just text.
      markdown = markdown.replace(/\[([^\]]+)\]\([^)]+\)/g, "$1");

      // Remove markdown images: transform ![alt](src) to just alt.
      markdown = markdown.replace(/!\[([^\]]*)\]\([^)]+\)/g, "$1");

      // Remove strong and emphasis markers: **text** or *text* become text.
      markdown = markdown.replace(/(\*\*|__)(.*?)\1/g, "$2");
      markdown = markdown.replace(/(\*|_)(.*?)\1/g, "$2");

      // Remove inline code markers and code blocks.
      markdown = markdown.replace(/`([^`]+)`/g, "$1");
      markdown = markdown.replace(/```[\s\S]*?```/g, "");

      // Remove ATX-style headings (# Heading).
      markdown = markdown.replace(/^#+\s*(.*?)$/gm, "$1");

      // Remove blockquote markers at the beginning of lines.
      markdown = markdown.replace(/^>\s*/gm, "");

      // Remove setext-style heading underlines.
      markdown = markdown.replace(/^(=+|-+)\s*$/gm, "");

      // Remove list markers.
      markdown = markdown.replace(/^(\s*)[-*+]\s+/gm, "$1");
      markdown = markdown.replace(/^(\s*)\d+\.\s+/gm, "$1");

      // Remove horizontal rules.
      markdown = markdown.replace(/^(\*\*\*|---|\*\*\*\*|____)\s*$/gm, "");

      // Remove table formatting.
      markdown = markdown.replace(/^\|(.+)\|$/gm, "$1");
      markdown = markdown.replace(/^[|:\-\s]+$/gm, "");

      // Remove escape characters for special Markdown syntax
      markdown = markdown.replace(/\\([[\]()*.#_`~+\-=|<>])/g, "$1"); // Handles most Markdown special chars
      markdown = markdown.replace(/(\d+)\\\./g, "$1."); // Special case for numbers followed by periods

      // Handle HTML entities
      markdown = markdown.replace(/&amp;/g, "&");
      markdown = markdown.replace(/&lt;/g, "<");
      markdown = markdown.replace(/&gt;/g, ">");
      markdown = markdown.replace(/&quot;/g, '"');
      markdown = markdown.replace(/&#39;/g, "'");

      // Clean up extra spaces and consecutive blank lines:
      // Trim whitespace for each line.
      markdown = markdown
        .split("\n")
        .map((line) => line.trim())
        .join("\n");

      // Replace multiple blank lines with a single blank line.
      markdown = markdown.replace(/\n{2,}/g, "\n\n");

      // Final trim to remove leading/trailing whitespace.
      markdown = markdown.trim();
    }

    return markdown;
  }

  /**
   * Modify the next AI reply once.
   * @param {string|null} replyText - Optional. Replace the entire AI reply if provided.
   * @param {string|null} appendText - Optional. Append extra text to the reply.
   */
  function modifyAIReply(replyText, appendText) {
    // Make sure MwaiAPI is available and supports addFilter
    if (!window.MwaiAPI || typeof MwaiAPI.addFilter !== 'function') return;

    // Track if the modification has already been used once
    let used = false;

    // Add a filter to modify the AI reply
    MwaiAPI.addFilter('ai.reply', function (reply, args) {
      // If already used once, skip modification for later replies
      if (used) return reply;
      used = true;

      // Start with the original reply
      let finalReply = reply;

      // If replyText is given, replace the entire reply
      if (replyText) finalReply = replyText;

      // If appendText is given, add it after the reply
      if (appendText) finalReply += ' ' + appendText;

      // Return the modified reply to display to the user
      return finalReply;
    });
  }

  // Expose globally under a namespaced object
  if (typeof window !== "undefined") {
    window.lexi ??= {};
    window.lexi.htmlToMarkdown = htmlToMarkdown;
    window.lexi.modifyAIReply = modifyAIReply;
  }
})();


