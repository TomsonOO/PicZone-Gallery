export interface BookAnalysisPort {
  getBookInformation(
    context: { title: string; author: string },
    userQuery: string,
  ): Promise<string>;
  generateVisualPrompt(
    context: { title: string; author: string },
    description: string,
  ): Promise<string>;
  discoverBookElements(
    context: { title: string; author: string },
    elementType: 'characters' | 'scenes' | 'themes',
  ): Promise<string[]>;
}
